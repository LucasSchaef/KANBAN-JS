<?php
if(isset($_POST['ch_id'], $_POST['ch_to'])) {
	$kanban = new kanban("kanban");
	$ret = $kanban->update($_POST['ch_id'], $_POST['ch_to']);
	echo $ret[0]."|".$ret[1];
}
 
class kanban {
	protected $id;
	private $db;
	public $data = array();
	public $noDataMsg = "<strong>No data found.</strong>";
	public $errors = array();
	
	public function __construct($id) {
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
		$ret = '<table class="table table-bordered">';
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
				$ret .= '<div class="single_task_head"><span style="text-color:'.$task["task_color"].'">'.$task["task_name"].'</span></div>';
				$ret .= '<div class="single_task_body"><span>'.$task["task_desc"].'</span></div>';
				$ret .= '<div class="single_task_footer">';
					switch($task["task_status"]) {
						case 0:
							$ret .= '<a href="#" class="task_change_status">'; // HIER GEHTS WEITER	
					}
				$ret .= '</div></div>';
			}
			return $ret;
		} else $this->errors[] = "The Taks have to be provided as array.";
	}
	
	public function update($ch_id, $ch_to) {
		
		switch($ch_to) {
			case 'col_todo':
				$new_col = 0;
				break;
			case 'col_doing':
				$new_col = 1;
				break;
			default:
				$new_col = 2;
				break;
		}
		
		if(array_key_exists($new_col, $this->data) && array_key_exists($new_col, $this->data[$new_col])) {
			return false;
		} else {
			$query = "UPDATE `tasks` SET `task_status`=".$new_col." WHERE `task_ID`=".$ch_id;
			if($this->db->query($query)) {
				if($this->getData()) {
					return array($ch_to, $this->createKanbanPart($this->data[$new_col]));
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
}
?>