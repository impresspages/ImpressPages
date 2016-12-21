<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;


class Table extends \Ip\Internal\Grid\Model
{

    /**
     * @var Config
     */
    protected $config = null;
    protected $subgridConfig = null;
    protected $request = null;

    public function __construct($config, $request)
    {
        $this->request = $request;
        $this->config = new Config($config);


        $hash = ipRequest()->getRequest('gridHash', '');


        $this->statusVariables = Status::parse($hash);

        $this->subgridConfig = $this->config->subgridConfig($this->statusVariables);


        $this->actions = $this->getActions();
    }

    public function handleMethod()
    {
        $request = $this->request->getRequest();

        if (empty($request['method'])) {
            throw new \Ip\Exception('Missing request data');
        }
        $method = $request['method'];

        if (in_array($method, array('update', 'create', 'delete', 'move'))) {
            $this->request->mustBePost();
        }

        if (in_array($method, array('update', 'create'))) {
            $data = $this->request->getPost();
            $params = $data;
        } elseif (in_array($method, array('search'))) {
            $data = $this->request->getQuery();
            $params = $data;
        } else {
            $data = $this->request->getRequest();
            $params = empty($data['params']) ? [] : $data['params'];
        }




        if ($this->subgridConfig->preventAction()) {
            $preventReason = call_user_func($this->subgridConfig->preventAction(), $method, $params, $this->statusVariables);
            if ($preventReason) {
                if (is_array($preventReason)) {
                    return $preventReason;
                } else {
                    return array(
                        Commands::showMessage($preventReason)
                    );
                }
            }
        }

        unset($params['method']);
        unset($params['aa']);


        switch ($method) {
            case 'init':
                return $this->init();
                break;
            case 'page':
                return $this->page($params);
                break;
            case 'setPageSize':
                return $this->setPageSize($params);
                break;
            case 'setLanguage':
                return $this->setLanguage($params);
                break;
            case 'delete':
                return $this->delete($params);
                break;
            case 'updateForm':
                $updateForm = $this->updateForm($params);
                $view = ipView('../view/updateForm.php', array('updateForm' => $updateForm))->render();
                return $view;
                break;
            case 'update':
                return $this->update($params);
                break;
            case 'move':
                return $this->move($params);
                break;
            case 'movePosition':
                return $this->movePosition($params);
                break;
            case 'create':
                return $this->create($params);
                break;
            case 'search':
                return $this->search($params);
                break;
            case 'subgrid':
                return $this->subgrid($params);
                break;
            case 'order':
                return $this->order($params);
                break;
        }
        return null;
    }




    protected function init()
    {

        $display = $this->getDisplay();
        $commands = [];
        $html = $display->fullHtml($this->statusVariables);
        $commands[] = Commands::setHtml($html);
        return $commands;
    }

    protected function page($params)
    {

        $statusVariables = $this->statusVariables;
        $pageVariableName = $this->subgridConfig->pageVariableName();
        if (empty($params['page'])) {
            throw new \Ip\Exception('Missing parameters');
        }

        $statusVariables[$pageVariableName] = $params['page'];
        $commands = [];
        $commands[] = Commands::setHash(Status::build($statusVariables));
        return $commands;
    }


    protected function setPageSize($params)
    {

        $statusVariables = $this->statusVariables;
        if (empty($params['pageSize'])) {
            throw new \Ip\Exception('Missing parameters');
        }

        $statusVariables['pageSize'] = $params['pageSize'];
        $pageVariableName = $this->subgridConfig->pageVariableName();
        $statusVariables[$pageVariableName] = 1;
        $commands = [];
        $commands[] = Commands::setHash(Status::build($statusVariables));
        return $commands;
    }

    protected function setLanguage($params)
    {

        $statusVariables = $this->statusVariables;
        if (empty($params['language'])) {
            throw new \Ip\Exception('Missing parameters');
        }

        $statusVariables['language'] = $params['language'];
        $commands = [];
        $commands[] = Commands::setHash(Status::build($statusVariables));
        return $commands;
    }

    protected function delete($params)
    {
        if (empty($params['id'])) {
            throw new \Ip\Exception('Missing parameters');
        }

        $commands = [];

        try {
            $actions = $this->getActions();
            $actions->delete($params['id']);

            //If we are not on the first page and we have removed the last record, move user to the previous page
            if (!empty($this->statusVariables[$this->config->pageVariableName()]) && $this->statusVariables[$this->config->pageVariableName()] > 1) {
                //We are not on the first page

                $db = new Db($this->subgridConfig, $this->statusVariables);
                $where = $db->buildSqlWhere();
                $pageSize = $this->subgridConfig->pageSize($this->statusVariables);
                $totalPages = ceil($db->recordCount($where) / $pageSize);
                if ($totalPages < $this->statusVariables[$this->config->pageVariableName()]) {
                    //set maximal page that has at least one record.
                    $statusVariables = $this->statusVariables;
                    $statusVariables[$this->config->pageVariableName()] = $totalPages;
                    $commands[] = Commands::setHash(Status::build($statusVariables));
                    return $commands;
                }

            }


            $display = $this->getDisplay();
            $html = $display->fullHtml($this->statusVariables);
            $commands[] = Commands::setHtml($html);
            return $commands;
        } catch (\Exception $e) {
            $commands[] = Commands::showMessage($e->getMessage());
        }

        return $commands;

    }

    protected function updateForm($params)
    {
        $display = $this->getDisplay();
        $updateForm = $display->updateForm($params['id']);
        return $updateForm;
    }

    protected function update($data)
    {
        if (empty($data[$this->subgridConfig->idField()])) {
            throw new \Ip\Exception('Missing parameters. Most likely \'AUTO_INCREMENT\' attribute is missing on the database id field');
        }

        $this->runTransformations($data);

        $recordId = $data[$this->subgridConfig->idField()];
        $display = $this->getDisplay();
        $updateForm = $display->updateForm($recordId);


        $errors = $updateForm->validate($data);

        if ($errors) {
            $data = array(
                'error' => 1,
                'errors' => $errors
            );
        } else {
            $newData = $updateForm->filterValues($data);

            $callables = $this->subgridConfig->beforeUpdate();
            if ($callables) {
                if (is_array($callables) && !is_callable($callables)) {
                    foreach($callables as $callable) {
                        call_user_func($callable, $recordId, $newData);
                    }
                } else {
                    call_user_func($callables, $recordId, $newData);
                }
            }

            $actions = $this->getActions();
            $actions->update($recordId, $newData);

            $callables = $this->subgridConfig->afterUpdate();
            if ($callables) {
                if (is_array($callables) && !is_callable($callables)) {
                    foreach($callables as $callable) {
                        call_user_func($callable, $recordId, $newData);
                    }
                } else {
                    call_user_func($callables, $recordId, $newData);
                }
            }

            $display = $this->getDisplay();
            $html = $display->fullHtml();
            $commands[] = Commands::setHtml($html);

            $data = array(
                'error' => 0,
                'commands' => $commands
            );
        }

        return $data;
    }

    protected function runTransformations(&$data)
    {
        foreach($this->subgridConfig->fields() as $field) {
            if (!empty($field['transformations']) && !empty($field['field']) && array_key_exists($field['field'], $data)) {
                foreach($field['transformations'] as $transformation) {
                    $transformationObject = $this->createTransformationObject($transformation);
                    $options = [];
                    if (is_array($transformation) && isset($transformation[1])) {
                        $options = $transformation[1];
                    }
                    $data[$field['field']] = $transformationObject->transform($data[$field['field']], $options);
                }
            }
        }
    }

    /**
     * @param $transformationSetting
     * @return \Ip\Internal\Grid\Model\Transformation
     * @throws \Ip\Exception
     */
    protected function createTransformationObject($transformationSetting)
    {
        if(is_array($transformationSetting)) {
            $transformationSetting = $transformationSetting[0];
        }

        if (is_object($transformationSetting)) {
            if (!$transformationSetting instanceof \Ip\Internal\Grid\Model\Transformation) {
                throw new \Ip\Exception('Transformation object has to implement Ip\Internal\Grid\Model\Table\Transformation interface');
            }
            return $transformationSetting;
        }
        if (is_string($transformationSetting)) {
            if (strpos($transformationSetting, '\\') === false) {
                $transformationSetting = 'Ip\\Internal\\Grid\\Model\\Transformation\\' . $transformationSetting;
            }
            $object = new $transformationSetting();
            return $object;
        }
    }


    protected function create($data)
    {
        $display = $this->getDisplay();
        $createForm = $display->createForm();

        $createForm->addAttribute('autocomplete', 'off');

        $this->runTransformations($data);

        $errors = $createForm->validate($data);

        if ($errors) {
            $data = array(
                'error' => 1,
                'errors' => $errors
            );
        } else {
            $newData = $createForm->filterValues($data);



            $callables = $this->subgridConfig->beforeCreate();
            if ($callables) {
                if (is_array($callables) && !is_callable($callables)) {
                    foreach($callables as $callable) {
                        call_user_func($callable, $newData);
                    }
                } else {
                    call_user_func($callables, $newData);
                }
            }

            $actions = $this->getActions();
            $recordId = $actions->create($newData);

            $callables = $this->subgridConfig->afterCreate();
            if ($callables) {
                if (is_array($callables) && !is_callable($callables)) {
                    foreach($callables as $callable) {
                        call_user_func($callable, $recordId, $newData);
                    }
                } else {
                    call_user_func($callables, $recordId, $newData);
                }
            }

            $display = $this->getDisplay();
            $html = $display->fullHtml();
            $commands[] = Commands::setHtml($html);

            $data = array(
                'error' => 0,
                'commands' => $commands
            );
        }

        return $data;
    }

    protected function move($params)
    {
        if (empty($params['id']) || empty($params['targetId']) || empty($params['beforeOrAfter'])) {
            throw new \Ip\Exception('Missing parameters');
        }

        if ($this->subgridConfig->beforeMove()) {
            call_user_func($this->subgridConfig->beforeMove(), $params['id']);
        }

        $id = $params['id'];
        $targetId = $params['targetId'];
        $beforeOrAfter = $params['beforeOrAfter'];

        $actions = $this->getActions();
        $actions->move($id, $targetId, $beforeOrAfter);
        $display = $this->getDisplay();
        $html = $display->fullHtml();
        $commands[] = Commands::setHtml($html);

        if ($this->subgridConfig->afterMove()) {
            call_user_func($this->subgridConfig->afterMove(), $params['id']);
        }
        return $commands;
    }

    protected function movePosition($params)
    {
        if (empty($params['id']) || !isset($params['position'])) {
            throw new \Ip\Exception('Missing parameters');
        }

        if ($this->subgridConfig->beforeMove()) {
            call_user_func($this->subgridConfig->beforeMove(), $params['id']);
        }

        $id = $params['id'];
        $position = $params['position'];

        $actions = $this->getActions();
        $actions->movePosition($id, $position);
        $display = $this->getDisplay();
        $html = $display->fullHtml();
        $commands[] = Commands::setHtml($html);

        if ($this->subgridConfig->afterMove()) {
            call_user_func($this->subgridConfig->afterMove(), $params['id']);
        }
        return $commands;
    }

    protected function search($data)
    {
        $statusVariables = $this->statusVariables;
        $display = $this->getDisplay();
        $searchForm = $display->searchForm([]);


        $errors = $searchForm->validate($data);

        if ($errors) {
            $data = array(
                'error' => 1,
                'errors' => $errors
            );
        } else {
            $newData = $searchForm->filterValues($data);


            foreach ($newData as $key => $value) {
                if (in_array($key, array('antispam', 'securityToken')) ) {
                    continue;
                }
                if($value == '') {
                    unset($statusVariables['s_' . $key]);
                    unset($statusVariables['s_' . $key . '_json']);
                    continue;
                }

                if (is_array($value)) {
                    foreach($value as $subkey => $subval) {
                        $statusVariables['s_' . $key . '_json'] = json_encode($value);
                        unset($statusVariables['s_' . $key]);
                    }
                } else {
                    $statusVariables['s_' . $key] = $value;
                }
            }

            foreach($searchForm->getFields() as $field) {
                if (!isset($newData[$field->getName()])) { //script above fails to remove search made based on checkbox as empty checkbox doesn't post anything
                    unset($statusVariables['s_' . $field->getName()]);
                    unset($statusVariables['s_' . $field->getName() . '_json']);
                }
            }

            $commands[] = Commands::setHash(Status::build($statusVariables));

            $data = array(
                'error' => 0,
                'commands' => $commands
            );
        }

        return $data;
    }


    protected function subgrid($params)
    {
        if (empty($params['gridId'])) {
            throw new \Ip\Exception('girdId GET variable missing');
        }
        if (empty($params['gridParentId'])) {
            throw new \Ip\Exception('girdParentId GET variable missing');
        }

        $newStatusVariables = Status::genSubgridVariables($this->statusVariables, $params['gridId'], $params['gridParentId']);

        $commands[] = Commands::setHash(Status::build($newStatusVariables));

        return $commands;
    }

    protected function getDisplay()
    {
        return new Display($this->config, $this->subgridConfig, $this->statusVariables);
    }

    protected function getActions()
    {
        return new Actions($this->subgridConfig, $this->statusVariables);
    }


    protected function order($params)
    {

        $statusVariables = $this->statusVariables;
        if (empty($params['order'])) {
            throw new \Ip\Exception('Missing parameters');
        }

        if (empty($statusVariables['order']) || $statusVariables['order'] != $params['order']) {
            //new field selected to order records. Use ascending order
            $statusVariables['order'] = $params['order'];
            unset($statusVariables['direction']);
        } else {
            //the same field has been clicked repeatedly.

            if (empty($statusVariables['direction']) || $statusVariables['direction'] == 'asc') {
                //the same field has been clicked twice. Change order direction to descending
                $statusVariables['order'] = $params['order'];
                $statusVariables['direction'] = 'desc';
            } else {
                //the same field has been clicked for the third time. Remove ordering
                unset($statusVariables['order']);
                unset($statusVariables['direction']);
            }
        }
        $commands = [];
        $commands[] = Commands::setHash(Status::build($statusVariables));
        return $commands;
    }


}
