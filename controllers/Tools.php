<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter3 Tools Controllers
 * Allows to work with migrations & create general app files.
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
        'library' =>'libraries'
    );

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        // Can be called only from the terminal
        if (!$this->input->is_cli_request()) {
            exit("Direct access denied! Use terminal.");
        }
        $this->load->dbforge(); 
        $this->load->library('migration');      
    }

    /**
     * Displays help menu
     * @print shows available actions
     */
    public function help() {
        $info = "Migration commands:\n";
        $info .= "tools migration \"name\"            Create new migration file\n";
        $info .= "tools migrate \"version\"           Run all migrations. The version number is optional.\n";
        $info .= "tools reset \"version\"             Reset all migrations. The version number is optional.\n\n";
        $info .= "File commands:\n";        
        $info .= "tools controller \"name\"           Create new controller.\n";
        $info .= "tools model \"name\"                Create new model.\n";
        $info .= "tools library \"name\"              Create new library.\n";       
        print $info . PHP_EOL;
    }

    /**
     * Runs all pending migration files.
     * The migration file number is optional. It's useful for rolling back migrations.
     * @params $number integer
     */
    public function migrate($number = null)
    {
        if ($number) {
            if ($this->migration->version($number)) {
                echo "Success: migration has been launched.\n" . PHP_EOL;
            } else {
                show_error($this->migration->error_string());                
            }
        }
        else {
            if ($this->migration->latest()) {
                echo "Success: migrations has been launched.\n" . PHP_EOL;
            } else {
                show_error($this->migration->error_string());
            }            
        }
    }

    /**
     * Creates a migration file
     * @params $name string
     */    
    public function migration($name)
    {
        $data['name'] = strtolower($name);
        $path = APPPATH . 'migrations/'. date('YmdHis') . '_' . $data['name'] .'.php';
        $migration_template = $this->load->view('tools/migrations', $data, TRUE);
        $migration = fopen($path, "w") or die("Error: unable to create migration file!\n" . PHP_EOL);
        fwrite($migration, $migration_template);
        fclose($migration);
        echo "Success: migration file has been created.\n" . PHP_EOL;
    }

    /**
     * Resets all migrations from database
     * @params $number string
     */    
    public function reset($number = null)
    {
        $v = ($number) ? $number : 0;
        $this->migration->version($v);
        echo "Success: migrations has been reseted.\n" . PHP_EOL;            
    }

    /**
     * Creates controller
     * @params $name string
     */
    public function controller($name)
    {
        $this->_create_file($this->file['controller'], $name);
    }

    /**
     * Creates model
     * @params $name string
     */
    public function model($name)
    {
        $this->_create_file($this->file['model'], $name);        
    }

    /**
     * Creates library
     * @params $name string
     */
    public function library($name)
    {
        $this->_create_file($this->file['library'], $name);         
    }

    /**
     * Creates application files
     * @params $type string
     * @params $name string 
     */
    protected function _create_file($type, $name)
    {
        $data['name'] = $name;
        $segment = $type . '/' . $name;
        $path = APPPATH . $segment . '.php';
        // Check similar file
        if (file_exists($path)) {
            exit("Error: '" . $segment . ".php' is already exist!\n" . PHP_EOL);
        }
        $template = $this->load->view('tools/' . $type, $data, TRUE);
        
        // Create file
        $file = fopen($path, "w") or die("Error: unable to create file!\n" . PHP_EOL);
        fwrite($file, $template);
        fclose($file);
        echo "Success: '" . $segment . ".php'  has been created.\n" . PHP_EOL;
    }  
}
