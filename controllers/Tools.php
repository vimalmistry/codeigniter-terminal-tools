<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Tools
 * Work with migrations & create general app files.
 */
class Tools extends CI_Controller
{
    /**
     * List of avalable file types
     * @var array
     */
    protected $file = array(
        'controller' => 'controllers',
        'model' =>'models',
        'library' =>'libraries',
        'helper' =>'helpers'
    );

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Can be called only from the terminal
        if (!$this->input->is_cli_request()) {
            exit('Direct access denied! Use terminal.');
        }

        $this->load->dbforge(); 
        $this->load->library('migration');      
    }

    /**
     * Display help menu
     * @print shows available actions
     */
    public function help() {
        $info = "Migration commands:\n";
        $info .= "tools migration \"name\"            Create new migration file\n";
        $info .= "tools migrate \"version\"           Run all migrations. The version number is optional.\n";
        $info .= "tools reset                       Reset all migrations.\n\n";
        $info .= "File commands:\n";        
        $info .= "tools controller \"name\"           Create new controller.\n";
        $info .= "tools model \"name\"                Create new model.\n";
        $info .= "tools library \"name\"              Create new library.\n";
        $info .= "tools helper \"name\"               Create new helper.\n";        

        print $info . PHP_EOL;
    }

    /**
     * Run all pending migration files.
     * The migration file number is optional. It's useful for rolling back migrations.
     * @params $number int
     */
    public function migrate($number = null)
    {
        if ($number) {
            if ($this->migration->version($number)) {
                echo 'Success: migration has been launched.' . PHP_EOL;
            }
            else {
                show_error($this->migration->error_string());                
            }
        }
        else {
            if ($this->migration->latest()) {
                echo 'Success: migrations has been launched.' . PHP_EOL;
            }
            else {
                show_error($this->migration->error_string());
            }            
        }
    }

    /**
     * Create a migration file
     * @params $name string
     */    
    public function migration($name)
    {
        $data['name'] = strtolower($name);

        $path = APPPATH . 'migrations/'. date('YmdHis') . '_' . $data['name'] .'.php';
        $migration_template = $this->load->view('tools/migrations', $data, TRUE);

        $migration = fopen($path, "w") or die('Error: unable to create migration file!' . PHP_EOL);
        fwrite($migration, $migration_template);
        fclose($migration);

        echo 'Success: migration file has been created.' . PHP_EOL;
    }

    /**
     * Reset all migrations from database
     */    
    public function reset()
    {
        $this->migration->version(0);
        echo 'Success: migrations has been reseted.' . PHP_EOL;            
    }

    /**
     * Create controller
     * @params $name string
     */
    public function controller($name)
    {
        $this->_create_file($this->file['controller'], $name);
    }

    /**
     * Create model
     * @params $name string
     */
    public function model($name)
    {
        $this->_create_file($this->file['model'], $name);        
    }

    /**
     * Create library
     * @params $name string
     */
    public function library($name)
    {
        $this->_create_file($this->file['library'], $name);         
    }

    /**
     * Create helper
     * @params $name string
     */
    public function helper($name)
    {
        $name = strtolower($name) .'_helper';
        $this->_create_file($this->file['helper'], $name);         
    }

    /**
     * Create application files
     * @params $type string
     * @params $name string 
     */
    protected function _create_file($type, $name)
    {
        $data['name'] = $name;
        $segment = $type . '/' . $name;
        $path = APPPATH . $segment . '.php';

        // Check file type & similar file
        if (file_exists($path)) {
            exit('Error: "' . $segment . '.php" is already exist!' . PHP_EOL);
        }

        $template = $this->load->view('tools/' . $type, $data, TRUE);
        
        // Create file
        $file = fopen($path, "w") or die('Error: unable to create file!' . PHP_EOL);
        fwrite($file, $template);
        fclose($file);

        echo 'Success: "' . $segment . '.php"  has been created.' . PHP_EOL;
    }  
}