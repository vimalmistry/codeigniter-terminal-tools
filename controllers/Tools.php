<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Can be called only from the terminal
        if (!$this->input->is_cli_request()) {
            exit('Direct access denied! Use terminal.');
        }

        $this->load->dbforge(); 
        $this->load->library('migration');
        $this->load->helper('file');       
    }

    public function help() {
        $info = "Available commands through \"php index.php\":\n";
        $info .= "tools migration \"file_name\" | Create new migration file\n";
        $info .= "tools migrate \"version_number\" | Run all migrations. The version number is optional.\n";
        $info .= "tools reset | Reset all migrations.\n";

        print $info . PHP_EOL;
    }

    public function migrate($version = null)
    {
        if ($version) {
            if ($this->migration->version($version)) {
                echo 'Success: migration run.';
            }
            else {
                show_error($this->migration->error_string());                
            }
        }
        else {
            if ($this->migration->latest()) {
                echo 'Success: migrations run.';
            }
            else {
                show_error($this->migration->error_string());
            }            
        }
    }

    public function migration($name)
    {
        $data['name'] = strtolower($name);
        $migration_file = APPPATH . 'migrations/'. date('YmdHis') . '_' . $name .'.php';
        $migration_template = $this->load->view('tools/migrations', $data, TRUE);

        if (write_file($migration_file, $migration_template, "w")) {
            echo 'Success: migration file has been created.';
        }
        else {
            echo 'Error: unable to create migration file!';
        }
    }

    public function reset()
    {
        $this->migration->version(0);
        echo 'Success: migrations reset.';            
    }  
}