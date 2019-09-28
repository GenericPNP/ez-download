<?php
namespace application\Libraries;

class Pagination 
{
    private $limit, $table_name, $db_extra, $db;
    public  $page, $offset;
    
    public function __construct($db) 
    {
        $this->db = $db;
    }
    
    public function setPagingParams($page, $limit, $table_name = NULL, $db_extra = NULL) 
    {
        $this->page = (int)$page;
        $this->limit = $limit;
        $this->table_name = $table_name;
        $this->db_extra = $db_extra;
        $this->getOffset();
    }
    
    public function maxPages() 
    {
        return ceil($this->getMaxResults()/$this->limit);
    }
    
    private function currentPage() 
    {
        if($this->page > $this->maxPages()+1)
        {
            return false;
        }
        if($this->page > 0) 
        {
            $this->page = (int)$this->page-1;
        } 
        else 
        {
            $this->page = 0;
        }
    }
    
    private function getMaxResults() 
    {
        if(is_numeric($this->table_name)) 
        {
            return $this->table_name;
        }
    }
    
    private function getOffset()
    {
        $this->getMaxResults();
        $this->currentPage();
        $this->offset = $this->limit*$this->page;
    }
    
}
?>