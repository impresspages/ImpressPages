/**
 * Register a PSR autoloader for a given namespace and directory
 * 
 * @param string $namespace
 * @param string $dir
 * @param string $type ('psr0' or 'psr4')
 * @return boolean
 * @throws Exception
 * @ref http://stackoverflow.com/a/35015933/2224584
 */
function generic_autoload($namespace, $dir, $type = 'psr4')
{
    switch ($type) {
        case 'psr0':
            $spl = '_';
            break;
        case 'psr4':
            $spl = '\\';
            break;
        default:
            throw new Exception('Invalid type; expected "psr0" or "psr4"');
    }
    $ns = trim($namespace, DIRECTORY_SEPARATOR.$spl);

    return spl_autoload_register(
        function($class) use ($ns, $dir, $spl)
        {
            // project-specific namespace prefix
            $prefix = $ns.$spl;

            // base directory for the namespace prefix
            $base_dir =  $dir . DIRECTORY_SEPARATOR;

            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // get the relative class name
            $relative_class = substr($class, $len);

            // replace the namespace prefix with the base directory, replace
            // namespace separators with directory separators in the relative 
            // class name, append with .php
            $file = $base_dir .
                str_replace($spl, DIRECTORY_SEPARATOR, $relative_class) .
                '.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        }
    );
}
