<?php
if(isset($_POST['ch_id'], $_POST['ch_to'], $_POST['ch_from'])) {
	$kanban = new kanban("kanban-table");
	$ret = $kanban->update($_POST['ch_id'], $_POST['ch_to'], $_POST['ch_from']);
	echo implode("|", $ret);
}
 
class kanban {
	protected $id;
	private $db;
	public $data = array();
	public $noDataMsg = "<strong>No data found.</strong>";
	public $errors = array();
	
	public function __construct($id = "kanban-table") {
		$this->id = $id;
		$this->connectDB("localhost", "root", "", "kanban");
	}
	
	public function create() {
		return $this->createTable();
	}
	
	public function getData() {
		
		$query = "SELECT * FROM `tasks`";
		
		$res = $this->db->query($query);
		
		if($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$this->data[$row["task_status"]][$row["task_ID"]] = $row;	
			}
			return true;
		} else return false;
	}
	
	private function createTable() {
		$ret = '<table class="table table-bordered" id="'.$this->id.'">';
		$ret .= '<thead><tr><th>ToDo</th><th>Doing</th><th>Done</th></tr></thead>';
		$ret .= '<tbody><tr>';
		
		if($this->getData() && is_array($this->data) && count($this->data) > 0) {
			$ret .= '<td id="col_todo" class="kanban_col">'.(array_key_exists(0, $this->data) ? $this->createKanbanPart($this->data[0]) : $this->noDataMsg).'</td>';
			$ret .= '<td id="col_doing" class="kanban_col">'.(array_key_exists(1, $this->data) ? $this->createKanbanPart($this->data[1]) : $this->noDataMsg).'</td>';
			$ret .= '<td id="col_done" class="kanban_col">'.(array_key_exists(2, $this->data) ? $this->createKanbanPart($this->data[2]) : $this->noDataMsg).'</td>';
		} else {
			$ret .= '<td class="text-centered" colspan="3">'.$this->noDataMsg.'</td>';		
		}
		$ret .= '</tbody></table>';
		return $ret;
	}
	
	private function createKanbanPart($tasks) {
		if(is_array($tasks)) {
			$ret = "";
			foreach($tasks as $key => $task) {
				$ret .= '<div class="single_task pull-left" id="'.$task['task_ID'].'" style="margin:10px; background-color:'.$task["task_color"].'">';
				$ret .= '<div class="single_task_head" style="background-color:'.$this->headColor($task['task_color']).'"><span style="text-color:'.$task["task_color"].'">'.$task["task_name"].'</span></div>';
				$ret .= '<div class="single_task_body"><span>'.$task["task_desc"].'</span></div>';
				$ret .= '</div>';
			}
			return $ret;
		} else $this->errors[] = "The Taks have to be provided as array.";
	}
	
	public function update($ch_id, $ch_to, $ch_from) {
		
		$cols = array("col_todo" => 0, "col_doing" => 1, "col_done" => 2);
		
		$old_col = $cols[$ch_from];
		$new_col = $cols[$ch_to];
		
		if(array_key_exists($new_col, $this->data) && array_key_exists($new_col, $this->data[$new_col])) {
			return false;
		} else {
			$query = "UPDATE `tasks` SET `task_status`=".$new_col." WHERE `task_ID`=".$ch_id;
			if($this->db->query($query)) {
				if($this->getData()) {
					return array(
								$ch_to, 
								$this->createKanbanPart($this->data[$new_col]), 
								(array_key_exists($old_col, $this->data) ? $this->createKanbanPart($this->data[$old_col]) : $this->noDataMsg)
								);
				} else {
					return "Konnte Daten nicht neu laden.";	
				}
			} else {
				return "Could not update task: ".$this->db->error;
			}
		} 
	}
	
	private function connectDB($server, $user, $password, $dbname) {
		$this->db = new MySQLi($server, $user, $password, $dbname);	
		
		if($this->db->connect_error)
			$this->errors[] = "Could not connect to database.<br /><strong>Error ".$this->db->connect_errno.": ".$this->db->connect-error;
	}
	
	private function closeDB() {
		$this->db->close();
	}
	
	private function headColor($hex) {
		$hex = str_replace('#', '', $hex);
		
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex, 0, 1), 2).str_repeat(substr($hex, 1, 1), 2).str_repeat(substr($hex, 2, 1), 2);
		}
	
		$colorVal = hexdec($hex);
        $rgbArray['red'] = (0xFF & ($colorVal >> 0x10)) - 20;
        $rgbArray['green'] = (0xFF & ($colorVal >> 0x8)) - 20;
        $rgbArray['blue'] = (0xFF & $colorVal) - 20;

    	return "rgb(".implode(",", $rgbArray).")";
	}
}
?>