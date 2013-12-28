<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


class Table extends \Ip\Grid\Model
{

    /**
     * @var Config
     */
    protected $config = null;

    public function __construct($config)
    {
        $this->config = new Config($config);

        $this->actions = new Actions($this->config);
    }

    public function handleMethod(\Ip\Request $request)
    {
        $data = $request->getRequest();
        if (empty($data['method'])) {
            throw new \Ip\CoreException('Missing request data');
        }
        $method = $data['method'];

        if (empty($data['hash'])) {
            $data['hash'] = '';
        }
        $hash = $data['hash'];

        if (isset($data['params'])) {
            $params = $data['params'];
        } else {
            $params = array();
        }
        $statusVariables = Status::parse($hash);


        if ($this->config->preventAction()) {
            $preventReason = call_user_func($this->config->preventAction(), $method, $params, $statusVariables);
            if ($preventReason) {
                return array(
                    Commands::showMessage($preventReason)
                );
            }
        }

        switch ($method) {
            case 'init':
                return $this->init($statusVariables);
                break;
            case 'page':
                return $this->page($params, $statusVariables);
                break;
            case 'delete':
                return $this->delete($params, $statusVariables);
                break;
            case 'updateForm':
                return $this->updateForm($params, $statusVariables)->render();
                break;
            case 'update':
                return $this->update($data, $statusVariables);
                break;
        }
    }

    protected function init($statusVariables)
    {
        $display = new Display($this->config);
        $commands = array();
        $html = $display->fullHtml($statusVariables);
        $commands[] = Commands::setHtml($html);
        return $commands;
    }

    protected function page($params, $statusVariables)
    {
        $statusVariables['page'] = $params['page'];
        $commands = array();
        $commands[] = Commands::setHash(Status::build($statusVariables));
        $display = new Display($this->config);
        $html = $display->fullHtml($statusVariables);
        $commands[] = Commands::setHtml($html);
        return $commands;
    }

    protected function delete($params, $statusVariables)
    {
        if ($this->config->beforeDelete()) {
            call_user_func($this->config->beforeDelete(), $params['id']);
        }

        try {
            $actions = new Actions($this->config);
            $actions->delete($params['id']);
            $display = new Display($this->config);
            $html = $display->fullHtml($statusVariables);
            $commands[] = Commands::setHtml($html);
            return $commands;
        } catch (\Exception $e) {
            $commands[] = Commands::showMessage($e->getMessage());
            return $commands;
        }

        if ($this->config->afterDelete()) {
            call_user_func($this->config->afterDelete(), $params['id']);
        }
    }

    protected function updateForm($params, $statusVariables)
    {
        $display = new Display($this->config);
        $updateForm = $display->updateForm($params['id']);
        return $updateForm;
    }

    protected function update($data, $statusVariables)
    {
        $display = new Display($this->config);
        $updateForm = $display->updateForm($data['recordId']);


        $errors = $updateForm->validate($data);

        if ($errors) {
            $data = array(
                'error' => 1,
                'errors' => $errors
            );
        } else {
            $actions = new Actions($this->config);
            $actions->update($data['recordId'], $updateForm->filterValues($data));

            $display = new Display($this->config);
            $html = $display->fullHtml($statusVariables);
            $commands[] = Commands::setHtml($html);

            $data = array(
                'error' => 0,
                'commands' => $commands
            );
        }

        return $data;
    }

}