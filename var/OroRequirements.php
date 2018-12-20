<?php
// @codingStandardsIgnoreFile

require_once __DIR__ . '/../var/SymfonyRequirements.php';

use Oro\Bundle\InstallerBundle\Process\PhpExecutableFinder;
use Oro\Bundle\RequireJSBundle\DependencyInjection\Configuration as RequireJSConfiguration;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Process\ProcessBuilder;

/**
 * This class specifies all requirements and optional recommendations that are necessary to run the Oro Application.
 */
class OroRequirements extends SymfonyRequirements
{
    const REQUIRED_PHP_VERSION  = '7.1.17';
    const REQUIRED_GD_VERSION   = '2.0';
    const REQUIRED_CURL_VERSION = '7.0';
    const REQUIRED_ICU_VERSION  = '3.8';

    const EXCLUDE_REQUIREMENTS_MASK = '/5\.[0-6]|7\.0/';

    /**
     * @param string $env
     */
    public function __construct($env = 'prod')
    {
        parent::__construct();

        $phpVersion  = phpversion();
        $gdVersion   = defined('GD_VERSION') ? GD_VERSION : null;
        $curlVersion = function_exists('curl_version') ? curl_version() : null;
        $icuVersion  = Intl::getIcuVersion();

        $this->addOroRequirement(
            version_compare($phpVersion, self::REQUIRED_PHP_VERSION, '>='),
            sprintf('PHP version must be at least %s (%s installed)', self::REQUIRED_PHP_VERSION, $phpVersion),
            sprintf(
                'You are running PHP version "<strong>%s</strong>", but Oro needs at least PHP "<strong>%s</strong>" to run.' .
                'Before using Oro, upgrade your PHP installation, preferably to the latest version.',
                $phpVersion,
                self::REQUIRED_PHP_VERSION
            ),
            sprintf('Install PHP %s or newer (installed version is %s)', self::REQUIRED_PHP_VERSION, $phpVersion)
        );

        $this->addOroRequirement(
            null !== $gdVersion && version_compare($gdVersion, self::REQUIRED_GD_VERSION, '>='),
            'GD extension must be at least ' . self::REQUIRED_GD_VERSION,
            'Install and enable the <strong>GD</strong> extension at least ' . self::REQUIRED_GD_VERSION . ' version'
        );

        $this->addOroRequirement(
            null !== $curlVersion && version_compare($curlVersion['version'], self::REQUIRED_CURL_VERSION, '>='),
            'cURL extension must be at least ' . self::REQUIRED_CURL_VERSION,
            'Install and enable the <strong>cURL</strong> extension at least ' . self::REQUIRED_CURL_VERSION . ' version'
        );

        $this->addOroRequirement(
            function_exists('mcrypt_encrypt'),
            'mcrypt_encrypt() should be available',
            'Install and enable the <strong>Mcrypt</strong> extension.'
        );

        if (function_exists('iconv')) {
            $this->addOroRequirement(
                false !== @iconv('utf-8', 'ascii//TRANSLIT', 'check string'),
                'iconv() must not return the false result on converting string "check string"',
                'Check the configuration of the <strong>iconv</strong> extension, '
                . 'as it may have been configured incorrectly'
                . ' (iconv(\'utf-8\', \'ascii//TRANSLIT\', \'check string\') must not return false).'
            );
        }

        $this->addOroRequirement(
            class_exists('Locale'),
            'intl extension should be available',
            'Install and enable the <strong>intl</strong> extension.'
        );

        $this->addOroRequirement(
            null !== $icuVersion && version_compare($icuVersion, self::REQUIRED_ICU_VERSION, '>='),
            'icu library must be at least ' . self::REQUIRED_ICU_VERSION,
            'Install and enable the <strong>icu</strong> library at least ' . self::REQUIRED_ICU_VERSION . ' version'
        );

        $this->addOroRequirement(
            class_exists('ZipArchive'),
            'zip extension should be installed',
            'Install and enable the <strong>Zip</strong> extension.'
        );

        $this->addRecommendation(
            class_exists('SoapClient'),
            'SOAP extension should be installed (API calls)',
            'Install and enable the <strong>SOAP</strong> extension.'
        );

        $this->addRecommendation(
            extension_loaded('tidy'),
            'Tidy extension should be installed to make sure that any HTML is correctly converted into a text representation.',
            'Install and enable the <strong>Tidy</strong> extension.'
        );

        $tmpDir = sys_get_temp_dir();
        $this->addRequirement(
            is_writable($tmpDir),
            sprintf('%s (sys_get_temp_dir()) directory must be writable', $tmpDir),
            sprintf(
                'Change the permissions of the "<strong>%s</strong>" directory ' .
                'or the result of <string>sys_get_temp_dir()</string> ' .
                'or add the path to php <strong>open_basedir</strong> list. ' .
                'So that it would be writable.',
                $tmpDir
            )
        );

        // Windows specific checks
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->addRecommendation(
                function_exists('finfo_open'),
                'finfo_open() should be available',
                'Install and enable the <strong>Fileinfo</strong> extension.'
            );

            $this->addRecommendation(
                class_exists('COM'),
                'COM extension should be installed',
                'Install and enable the <strong>COM</strong> extension.'
            );
        }

        // Unix specific checks
        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->addRequirement(
                $this->checkFileNameLength(),
                'Maximum supported filename length must be greater or equal 242 characters.' .
                ' Make sure that the cache folder is not inside the encrypted directory.',
                'Move <strong>var/cache</strong> folder outside encrypted directory.',
                'Maximum supported filename length must be greater or equal 242 characters.' .
                ' Move var/cache folder outside encrypted directory.'
            );
        }

        // Web installer specific checks
        if ('cli' !== PHP_SAPI) {
            $output = $this->checkCliRequirements();

            $requirement = new CliRequirement(
                !$output,
                'Requirements validation for PHP CLI',
                'If you have multiple PHP versions installed, you need to configure ORO_PHP_PATH variable with PHP binary path used by web server'
            );

            $requirement->setOutput($output);

            $this->add($requirement);
        }

        $baseDir = realpath(__DIR__ . '/..');
        $mem     = $this->getBytes(ini_get('memory_limit'));

        $this->addPhpIniRequirement(
            'memory_limit',
            function ($cfgValue) use ($mem) {
                return $mem >= 512 * 1024 * 1024 || -1 == $mem;
            },
            false,
            'memory_limit should be at least 512M',
            'Set the "<strong>memory_limit</strong>" setting in php.ini<a href="#phpini">*</a> to at least "512M".'
        );

        $jsEngine = RequireJSConfiguration::getDefaultJsEngine();

        $this->addRecommendation(
            $jsEngine ? true : false,
            $jsEngine ? "A JS Engine ($jsEngine) is installed" : 'JSEngine such as NodeJS should be installed',
            'Install <strong>JSEngine</strong>.'
        );

        $this->addOroRequirement(
            is_writable($baseDir . '/public/uploads'),
            'public/uploads/ directory must be writable',
            'Change the permissions of the "<strong>public/uploads/</strong>" directory so that the web server can write into it.'
        );
        $this->addOroRequirement(
            is_writable($baseDir . '/public/media'),
            'public/media/ directory must be writable',
            'Change the permissions of the "<strong>public/media/</strong>" directory so that the web server can write into it.'
        );
        $this->addOroRequirement(
            is_writable($baseDir . '/public/bundles'),
            'public/bundles/ directory must be writable',
            'Change the permissions of the "<strong>public/bundles/</strong>" directory so that the web server can write into it.'
        );
        $this->addOroRequirement(
            is_writable($baseDir . '/var/attachment'),
            'var/attachment/ directory must be writable',
            'Change the permissions of the "<strong>var/attachment/</strong>" directory so that the web server can write into it.'
        );
        $this->addOroRequirement(
            is_writable($baseDir . '/var/import_export'),
            'var/import_export/ directory must be writable',
            'Change the permissions of the "<strong>var/import_export/</strong>" directory so that the web server can write into it.'
        );

        if (is_dir($baseDir . '/public/js')) {
            $this->addOroRequirement(
                is_writable($baseDir . '/public/js'),
                'public/js directory must be writable',
                'Change the permissions of the "<strong>public/js</strong>" directory so that the web server can write into it.'
            );
        }

        if (is_dir($baseDir . '/public/css')) {
            $this->addOroRequirement(
                is_writable($baseDir . '/public/css'),
                'public/css directory must be writable',
                'Change the permissions of the "<strong>public/css</strong>" directory so that the web server can write into it.'
            );
        }

        if (!is_dir($baseDir . '/public/css') || !is_dir($baseDir . '/public/js')) {
            $this->addOroRequirement(
                is_writable($baseDir . '/public'),
                'public directory must be writable',
                'Change the permissions of the "<strong>public</strong>" directory so that the web server can write into it.'
            );
        }

        if (is_file($baseDir . '/config/parameters.yml')) {
            $this->addOroRequirement(
                is_writable($baseDir . '/config/parameters.yml'),
                'config/parameters.yml file must be writable',
                'Change the permissions of the "<strong>config/parameters.yml</strong>" file so that the web server can write into it.'
            );
        }
    }

    /**
     * Adds an Oro specific requirement.
     *
     * @param Boolean     $fulfilled Whether the requirement is fulfilled
     * @param string      $testMessage The message for testing the requirement
     * @param string      $helpHtml The help text formatted in HTML for resolving the problem
     * @param string|null $helpText The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addOroRequirement($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new OroRequirement($fulfilled, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Get the list of mandatory requirements (all requirements excluding PhpIniRequirement)
     *
     * @return array
     */
    public function getMandatoryRequirements()
    {
        return array_filter(
            $this->getRequirements(),
            function ($requirement) {
                return !($requirement instanceof PhpIniRequirement)
                    && !($requirement instanceof OroRequirement)
                    && !($requirement instanceof CliRequirement);
            }
        );
    }

    /**
     * Get the list of PHP ini requirements
     *
     * @return array
     */
    public function getPhpIniRequirements()
    {
        return array_filter(
            $this->getRequirements(),
            function ($requirement) {
                return $requirement instanceof PhpIniRequirement;
            }
        );
    }

    /**
     * Get the list of Oro specific requirements
     *
     * @return array
     */
    public function getOroRequirements()
    {
        return array_filter(
            $this->getRequirements(),
            function ($requirement) {
                return $requirement instanceof OroRequirement;
            }
        );
    }

    /**
     * @return array
     */
    public function getCliRequirements()
    {
        return array_filter(
            $this->getRequirements(),
            function ($requirement) {
                return $requirement instanceof CliRequirement;
            }
        );
    }

    /**
     * @param  string $val
     * @return int
     */
    protected function getBytes($val)
    {
        if (empty($val)) {
            return 0;
        }

        preg_match('/([\-0-9]+)[\s]*([a-z]*)$/i', trim($val), $matches);

        if (isset($matches[1])) {
            $val = (int)$matches[1];
        }

        switch (strtolower($matches[2])) {
            case 'g':
            case 'gb':
                $val *= 1024;
            // no break
            case 'm':
            case 'mb':
                $val *= 1024;
            // no break
            case 'k':
            case 'kb':
                $val *= 1024;
            // no break
        }

        return (float)$val;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequirements()
    {
        $requirements = parent::getRequirements();

        foreach ($requirements as $key => $requirement) {
            if (!$requirement instanceof OroRequirement) {
                $testMessage = $requirement->getTestMessage();
                if (preg_match_all(self::EXCLUDE_REQUIREMENTS_MASK, $testMessage, $matches)) {
                    unset($requirements[$key]);
                }
            }
        }

        return $requirements;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecommendations()
    {
        $recommendations = parent::getRecommendations();

        foreach ($recommendations as $key => $recommendation) {
            $testMessage = $recommendation->getTestMessage();
            if (preg_match_all(self::EXCLUDE_REQUIREMENTS_MASK, $testMessage, $matches)) {
                unset($recommendations[$key]);
            }
        }

        return $recommendations;
    }

    /**
     * @return bool
     */
    protected function checkFileNameLength()
    {
        $getConf = new ProcessBuilder(array('getconf', 'NAME_MAX', __DIR__));
        $getConf = $getConf->getProcess();

        if (isset($_SERVER['PATH'])) {
            $getConf->setEnv(array('PATH' => $_SERVER['PATH']));
        }
        $getConf->run();

        if ($getConf->getErrorOutput()) {
            // getconf not installed
            return true;
        }

        $fileLength = trim($getConf->getOutput());

        return $fileLength >= 242;
    }

    /**
     * @return null|string
     */
    protected function checkCliRequirements()
    {
        $finder  = new PhpExecutableFinder();
        $command = sprintf(
            '%s %soro-check.php',
            $finder->find(),
            __DIR__ . DIRECTORY_SEPARATOR
        );

        return shell_exec($command);
    }
}

class OroRequirement extends Requirement
{
}

class CliRequirement extends Requirement
{
    /**
     * @var string
     */
    protected $output;

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }
}
