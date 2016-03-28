<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends CI_Controller
{
    protected $file = array(
        'controller' => 'controllers',
        'model' =>'models',
        'library' =>'libraries'
    );

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
     * Display the help menu
     * @print shows available actions
     */
    public function help() {
        $info = "CI migration commands:\n";
        $info .= "php index.php tools migration \"name\"            Create new migration file\n";
        $info .= "php index.php tools migrate \"version\"           Run all migrations. The version number is optional.\n";
        $info .= "php index.php tools reset                       Reset all migrations.\n\n";
        $info .= "CI file commands:\n";        
        $info .= "php index.php tools controller \"name\"           Create new controller.\n";
        $info .= "php index.php tools model \"name\"                Create new model.\n";
        $info .= "php index.php tools library \"name\"              Create new library.\n";
        $info .= "php index.php tools delete \"file\" \"name\"        Delete file.";

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
     * Create a migration file.
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
     * Reset all migrations from database.
     */    
    public function reset()
    {
        $this->migration->version(0);
        echo 'Success: migrations has been reseted.' . PHP_EOL;            
    }

    /**
     * Available actions for application files:
     * => controller
     * => model
     * => library
     *
     * @params $name string. It's a file name.
     * @params $key string. Use "-rm" to remove your created file.
     */
    public function controller($name, $key = null)
    {
        ($key === '-rm') ?
        $this->_delete($this->file['controller'], $name) :
        $this->_create($this->file['controller'], $name);
    }

    public function model($name, $key = null)
    {
        ($key === '-rm') ?
        $this->_delete($this->file['model'], $name) :
        $this->_create($this->file['model'], $name);        
    }

    public function library($name, $key = null)
    {
        ($key === '-rm') ?
        $this->_delete($this->file['library'], $name) :
        $this->_create($this->file['library'], $name);         
    }

    /**
     * Create application files.
     * @params $type string
     * @params $name string 
     */
    protected function _create($type, $name)
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

    /**
     * Delete application files.
     * @params $type string
     * @params $name string  
     */
    protected function _delete($type, $name)
    {
        $segment = $type . '/' . $name;
        $file = APPPATH . $segment . '.php';

        // Check similar file
        if (file_exists($file)) {
            // Delete file
            unlink($file) or die('Error: unable to delete file!' . PHP_EOL);
            echo 'Success: "' . $segment . '.php" has been deleted.' . PHP_EOL;
        }
        else {
            exit('Error: unable to delete file!' . PHP_EOL);
        }  
    }    
}