<?php
class Costumers
{
    /**
     *
     */
    public function __construct()
    {
    }

    /**
     *
     */
    public function __destruct()
    {
    }
    
    /**
     * Set friendly columns\' names to order tables\' entries
     */
    public function setOrderingValues()
    {
        $ordering = [
            'id' => 'ID',
            'task_name' => 'Title',
            'start_date' => 'Start Date',
            'end_date' => 'End Date'
        ];

        return $ordering;
    }
}
?>
