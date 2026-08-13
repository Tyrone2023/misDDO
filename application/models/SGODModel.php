<?php
class SGODModel extends CI_Model
{

	function cPublic()
	{
		$query = $this->db->query("select count(recID) as schoolCounts from schools where schoolType='Public'");
		return $query->result();
	}

	function cPrivate()
	{
		$query = $this->db->query("select count(recID) as schoolCounts from schools where schoolType='Private'");
		return $query->result();
	}

	function schoolDetails($schoolID)
	{
		$query = $this->db->query("select * from schools where schoolID='" . $schoolID . "'");
		return $query->result();
	}

	function aSectionAccomplishments($section)
	{
		$query = $this->db->query("select count(id) as Counts from sgod_accomplishments where section='" . $section . "'");
		return $query->result();
	}

	function totalSectionUsers($section)
	{
		$query = $this->db->query("select count(username) as Counts from sgod_users where section='" . $section . "'");
		return $query->result();
	}

	public function update_sections()
	{
		$data = array(
			'sectionName' => $this->input->post('sectionName'),
			'sectionHead' => $this->input->post('sectionHead'),
			'sectionHeadPosition' => $this->input->post('sectionHeadPosition'),
			'secGroup' => $this->input->post('secGroup'),
			'member' => $this->input->post('member')
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_sections', $data);
	}

	public function insert_sections()
	{
		$data = array(
			'sectionName' => $this->input->post('sectionName'),
			'sectionHead' => $this->input->post('sectionHead'),
			'sectionHeadPosition' => $this->input->post('sectionHeadPosition'),
			'secGroup' => $this->input->post('secGroup'),
			'member' => $this->input->post('member')
		);

		return $this->db->insert('sgod_sections', $data);
	}




	public function viewSections()
	{
		$query = $this->db->query("select * from sgod_sections  order by sectionName");
		return $query->result();
	}
	public function viewSectionsChecking($param)
	{
		$query = $this->db->query("select * from sgod_sections where secGroup='" . $param . "' and sectionName !='Chief - SGOD' order by sectionName");
		return $query->result();
	}

	function accombyid($id)
	{
		$this->db->where('id', $id);
		$result = $this->db->get('sgod_accomplishments');
		return $result->result();
	}

	public function one_cond_count($table, $col, $val)
	{
		$this->db->where($col, $val);
		$result = $this->db->get($table);
		return $result;
	}





	//$result = $this->db->query('SELECT * FROM mis.sgod_accomplishments where quarter="'.$quarter.'" and year='.$year.' and monthAcc="'.$month.'" and weekAcc='.$week.' and section="'.$sec.'" and activityCategory="'.$ac.'"');

	public function checking($quarter, $year, $week, $month, $sec, $ac)
	{
		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);
		$this->db->where("section", $sec);
		$this->db->where("monthAcc", $month);
		$this->db->where("weekAcc", $week);
		$result = $this->db->get('sgod_accomplishments');
		return $result->result();
	}

	function viewSecAccomplishments($section, $secGroup)
	{
		$this->db->where('section', $section);
		$this->db->where('secGroup', $secGroup);
		$result = $this->db->get('sgod_accomplishments');
		return $result->result();
	}

	function get_table_where($id, $table)
	{
		$this->db->where('acc_id', $id);
		$result = $this->db->get($table);
		return $result->result();
	}

	public function get_accomplishment()
	{
		$this->db->where('year', '2023');
		$result = $this->db->get('sgod_accomplishments');

		return $result->row_array();
	}
	public function get_all_data_where_single($table, $col, $val)
	{
		$this->db->where($col, $val);
		$result = $this->db->get($table);

		return $result->result_array();
	}

	public function get_accomplishment_by($cat, $quarter, $year, $week, $month, $secGroup)
	{

		$sec = $this->input->post('sec');

		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);
		$this->db->where("section", $sec);
		$this->db->where("secGroup", $secGroup);

		$this->db->where("activityCategory", $cat);

		if ($this->input->post('weekAcc') != "") {
			$this->db->where("monthAcc", $month);
			$this->db->where("weekAcc", $week);
			$this->db->where("secGroup", $secGroup);
		}
		$result = $this->db->get('sgod_accomplishments');

		return $result->result_array();
	}
	public function get_accomplishment_by_row($quarter, $year, $week, $month)
	{
		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);

		$user = $this->session->userdata('acctLevel');
		if ($user != 'System Administrator') {
			$sec = $this->session->userdata('section');
		} else {
			$sec = $this->input->post('sec');
		}

		$this->db->where("section", $sec);


		if ($this->input->post('weekAcc') != "") {
			$this->db->where("monthAcc", $month);
			$this->db->where("weekAcc", $week);
			$this->db->group_by("section");
		}
		$result = $this->db->get('sgod_accomplishments');

		return $result->row();
	}
	public function get_accomplish_by_row($quarter, $year, $week, $month)
	{
		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);

		if ($this->input->post('weekAcc') != "") {
			$this->db->where("monthAcc", $month);
			$this->db->where("weekAcc", $week);
			$this->db->group_by("section");
		}
		$result = $this->db->get('sgod_accomplishments');

		return $result->row();
	}

	public function get_accomplish_by_row_year($year)
	{
		$this->db->where("year", $year);
		$result = $this->db->get('sgod_accomplishments');
		return $result->row();
	}
	public function get_all_accomplishment($cat, $quarter, $year, $week, $month, $secGroup)
	{

		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);
		$this->db->where("activityCategory", $cat);
		$this->db->where("secGroup", $secGroup);

		if ($this->input->post('weekAcc') != "") {
			$this->db->where("monthAcc", $month);
			$this->db->where("weekAcc", $week);
			$this->db->where("secGroup", $secGroup);
		}
		$this->db->group_by('section');
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function get_year_accomplishment($cat, $year, $secGroup)
	{

		$this->db->where("year", $year);
		$this->db->where("activityCategory", $cat);
		$this->db->where("secGroup", $secGroup);
		$this->db->group_by('section');
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function get_all_acc_by_section($cat, $quarter, $year, $week, $month, $section)
	{

		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);
		$this->db->where("activityCategory", $cat);
		$this->db->where("section", $section);

		if ($this->input->post('weekAcc') != "") {
			$this->db->where("monthAcc", $month);
			$this->db->where("weekAcc", $week);
		}
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function get_all_acc_by_section_year($cat, $year, $section)
	{

		$this->db->where("year", $year);
		$this->db->where("activityCategory", $cat);
		$this->db->where("section", $section);
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function get_accomplishment_group_by_section($quarter, $year, $week)
	{

		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);

		if ($this->input->post('weekAcc') != "") {
			$this->db->where("weekAcc", $week);
		}
		$this->db->group_by('section');
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function get_acc_group_by_section_year($year)
	{

		$this->db->where("year", $year);

		$this->db->group_by('section');
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function get_acc_group_by_section($quarter, $year, $week, $month)
	{

		$this->db->where("quarter", $quarter);
		$this->db->where("year", $year);

		if ($this->input->post('weekAcc') != "") {
			$this->db->where("weekAcc", $week);
			$this->db->where("monthAcc", $month);
		}

		$this->db->group_by('section');
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function get_aip($school_id, $fy, $bcode)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->group_by('pia');
		// Cluster the plan by pillar so the report is separated per pillar
		$this->db->order_by('pillar', 'ASC');
		$this->db->order_by('io', 'ASC');
		$result = $this->db->get('sgod_aip');

		return $result->result();
	}

	public function get_smea($school_id, $fy, $bcode)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->group_by('pillar');
		$result = $this->db->get('sgod_aip');

		return $result->result();
	}

	public function get_aip_sip_project($school_id, $fy, $pia, $bcode)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->where("pia", $pia);
		$this->db->group_by('sip_project');
		$result = $this->db->get('sgod_aip');
		return $result->result();
	}

	public function get_aip_by_sip($school_id, $fy, $sip, $bcode)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->where("sip_project", $sip);
		$result = $this->db->get('sgod_aip');
		return $result->result();
	}

	public function get_aip_row($school_id, $fy, $bcode)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->group_by('fy');
		$result = $this->db->get('sgod_aip');

		$row = $result->row();

		// A batch can have an allocation (and even an approved submission) while no line
		// items were ever encoded for it. The report views build their headings and links
		// off this row, so hand back the keys that were asked for instead of null.
		if (empty($row)) {
			$row = new stdClass();
			$row->id        = null;
			$row->school_id = $school_id;
			$row->fy        = $fy;
			$row->b_code    = $bcode;
		}

		return $row;
	}


	public function aip_related_row($table, $school_id, $fy, $bcode)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->group_by('fy');
		$result = $this->db->get($table);
		return $result->row();
	}

	public function aip_category($table, $school_id, $fy, $bcode, $cat)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->where("category", $cat);
		$this->db->where("budget_source", 'MOOE');
		$result = $this->db->get($table);
		return $result->result();
	}

	public function aip_category_liq($table, $school_id, $fy, $bcode, $cat, $mm)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("bcode", $bcode);
		$this->db->where("category", $cat);
		$this->db->where("mm", $mm);
		//$this->db->where("budget_source", 'MOOE');
		$result = $this->db->get($table);
		return $result->result();
	}

	public function aip_category_sned($table, $school_id, $fy, $bcode, $cat)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->where("category", $cat);
		//$this->db->where("budget_source", 'MOOE');
		$result = $this->db->get($table);
		return $result->result();
	}

	public function aip_category_sbfp($table, $school_id, $fy, $bcode, $cat)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $bcode);
		$this->db->where("category", $cat);
		//$this->db->where("budget_source", 'MOOE');
		$result = $this->db->get($table);
		return $result->result();
	}

	public function get_all_aip($school_id, $fy, $pia, $code)
	{
		$this->db->where("school_id", $school_id);
		$this->db->where("fy", $fy);
		$this->db->where("b_code", $code);
		$this->db->where("pia", $pia);
		$result = $this->db->get('sgod_aip');

		return $result->result();
	}



	public function get_all_aip_by()
	{
		$this->db->where("school_id", $this->input->post('school_id'));

		$fy = $this->input->post('fy');
		$pillar = $this->input->post('pillar');
		$domain = $this->input->post('domain');
		$strand = $this->input->post('strand');
		$pias = $this->input->post('pias');

		if ($fy != "") {
			$this->db->where("fy", $fy);
		}
		if ($pillar != "") {
			$this->db->where("pillar", $pillar);
		}
		if ($domain != "") {
			$this->db->where("domain", $domain);
		}
		if ($strand != "") {
			$this->db->where("strand", $strand);
		}
		if ($pias != "") {
			$this->db->where("pia", $pias);
		}
		$result = $this->db->get('sgod_aip');

		return $result->result();
	}

	public function get_single_table_by_id($col, $table, $param)
	{
		$this->db->where($col, $param);
		$result = $this->db->get($table);

		return $result->row_array();
	}
	public function get_single_by_id($col, $table, $param)
	{
		$this->db->where($col, $param);
		$result = $this->db->get($table);

		return $result->row();
	}
	public function get_all_by_row($col, $table, $param)
	{
		$this->db->where($col, $param);
		$result = $this->db->get($table);

		return $result->result();
	}

	public function get_all_by_row2($col, $table, $secGroup, $col2, $section)
	{
		$this->db->where($col, $secGroup);
		$this->db->where($col2, $section);
		$result = $this->db->get($table);

		return $result->result();
	}

	public function count_table_row($table)
	{
		$result = $this->db->get($table);
		return $result;
	}
	public function count_sections($table, $param)
	{
		$this->db->where("secGroup", $param);
		$result = $this->db->get($table);
		return $result;
	}

	public function count_sec_users($table, $param)
	{
		$this->db->where("secGroup", $param);
		$result = $this->db->get($table);
		return $result;
	}

	public function count_sec_accomplishments($table, $param)
	{
		$this->db->where("secGroup", $param);
		$result = $this->db->get($table);
		return $result;
	}

	public function get_all($table)
	{
		$result = $this->db->get($table);
		return $result->result();
	}

	public function get_all_orderby($table, $col, $val)
	{
		$this->db->order_by($col, $val);
		$result = $this->db->get($table);
		return $result->result();
	}
	public function one_cond_orderby($table, $ccol, $cval, $col, $val)
	{
		$this->db->where($ccol, $cval);
		$this->db->order_by($col, $val);
		$result = $this->db->get($table);
		return $result->result();
	}


	public function two_cond_orderby($table, $ccol, $cval, $ccol2, $cval2, $col, $val)
	{
		$this->db->where($ccol, $cval);
		$this->db->where($ccol2, $cval2);
		$this->db->order_by($col, $val);
		$this->db->limit(1);
		$result = $this->db->get($table);
		return $result->row();
	}



	public function count_all($table, $col, $val)
	{
		$query = $this->db->get_where($table, array($col => $val));
		return $query;
	}

	public function count_all_two_cond($table, $col, $val, $col2, $val2)
	{
		$query = $this->db->get_where($table, array($col => $val, $col2 => $val2));
		return $query;
	}



	// public function count_all($table){
	// 	$query = $this->db->get_where($table, array('company_id' => $this->session->com_id,'status' => '0'));
	// 	return $query;
	// }



	public function one_cond($table, $col, $val)
	{
		$this->db->where($col, $val);
		$result = $this->db->get($table);
		return $result->result();
	}

	public function no_cond($table)
	{
		$result = $this->db->get($table);
		return $result->result();
	}

	public function two_cond_rca($table, $col, $val, $mon)
	{
		$this->db->where($col, $val);
		$this->db->where($mon . ' !=', 0);
		$result = $this->db->get($table);
		return $result->result();
	}


	public function two_cond_not_equal_sencod($table, $col, $val, $col2, $val2)
	{
		$this->db->where($col, $val);
		$this->db->where($col2 . ' !=', $val2);
		$result = $this->db->get($table);
		return $result->result();
	}

	public function three_cond_not_equal_cond($table, $col, $val, $col2, $val2, $col3, $val3)
	{
		$this->db->where($col, $val);
		$this->db->where($col2, $val2);
		$this->db->where($col3 . ' !=', $val3);
		$result = $this->db->get($table);
		return $result->result();
	}


	public function two_cond_count_not_equal_cond($table, $col, $val, $col2, $val2)
	{
		$this->db->where($col, $val);
		$this->db->where($col2 . ' !=', $val2);
		$result = $this->db->get($table);
		return $result;
	}

	public function three_cond_count_not_equal_cond($table, $col, $val, $col2, $val2, $col3, $val3)
	{
		$this->db->where($col, $val);
		$this->db->where($col2, $val2);
		$this->db->where($col3 . ' !=', $val3);
		$result = $this->db->get($table);
		return $result;
	}

	public function one_cond_not_equal($table, $col, $val)
	{
		$this->db->where($col . ' !=', $val);
		$result = $this->db->get($table);
		return $result->result();
	}
	public function two_cond($table, $col, $val, $col2, $val2)
	{
		$this->db->where($col, $val);
		$this->db->where($col2, $val2);
		$result = $this->db->get($table);
		return $result->result();
	}
	public function two_cond_group($table, $col, $val, $col2, $val2, $valcol)
	{
		$this->db->where($col, $val);
		$this->db->where($col2, $val2);
		$this->db->group_by($valcol);
		$result = $this->db->get($table);
		return $result->result();
	}
	public function no_cond_group_by($table, $valcol)
	{
		$this->db->group_by($valcol);
		$result = $this->db->get($table);
		return $result->result();
	}

	public function three_cond($table, $col, $val, $col2, $val2, $col3, $val3)
	{
		$this->db->where($col, $val);
		$this->db->where($col2, $val2);
		$this->db->where($col3, $val3);
		$result = $this->db->get($table);
		return $result->result();
	}

	public function one_cond_where_or($table, $col, $val, $col2, $val2)
	{
		$this->db->where($col, $val);
		$this->db->or_where($col2, $val2);
		$result = $this->db->get($table);
		return $result->result();
	}

	public function two_cond_row($table, $col, $val, $col2, $val2)
	{
		$this->db->where($col, $val);
		$this->db->where($col2, $val2);
		$result = $this->db->get($table);
		return $result->row();
	}

	public function get_last_record($table)
	{
		$this->db->order_by('id', 'DESC')->limit(1);
		$result = $this->db->get($table);
		return $result->row();
	}




	public function three_cond_row($table, $col, $val, $col2, $val2, $col3, $val3)
	{
		$this->db->where($col, $val);
		$this->db->where($col2, $val2);
		$this->db->where($col3, $val3);
		$result = $this->db->get($table);
		return $result->row();
	}

	public function one_cond_row($table, $col, $val)
	{
		$this->db->where($col, $val);
		$result = $this->db->get($table);
		return $result->row();
	}

	public function get_data_by_id($table, $col, $val)
	{
		$this->db->where($col, $val);
		$result = $this->db->get($table);
		return $result->row();
	}
	public function one_cond_last_record($table, $col, $val, $wcol, $wval)
	{
		$this->db->where($wcol, $wval);
		$this->db->order_by($col, $val);
		$this->db->limit(1);
		$result = $this->db->get($table);
		return $result->row();
	}
	public function last_record($table, $col, $val)
	{
		$this->db->order_by($col, $val);
		$this->db->limit(1);
		$result = $this->db->get($table);
		return $result->row();
	}


	public function aip($table, $val1, $val2, $val3, $val4)
	{
		$this->db->where('school_id', $val1);
		$this->db->where('fy', $val2);
		$this->db->where('b_code', $val3);
		$this->db->where('category', $val4);
		$this->db->where('budget_source', 'MOOE');

		$result = $this->db->get($table);
		return $result->result();
	}

	public function aipv2($table, $val1, $val2, $val3, $val4)
	{
		$this->db->where('school_id', $val1);
		$this->db->where('fy', $val2);
		$this->db->where('b_code', $val3);
		$this->db->where('category', $val4);

		$result = $this->db->get($table);
		return $result->result();
	}

	public function aipv3($table, $val1, $val2, $val3, $val4, $bs, $bs2)
	{
		$this->db->where('school_id', $val1);
		$this->db->where('fy', $val2);
		$this->db->where('b_code', $val3);
		$this->db->where('category', $val4);
		$this->db->where('budget_source', $bs);
		$this->db->or_where('budget_source', $bs2);
		$result = $this->db->get($table);
		return $result->result();
	}



	public function delete($segment, $col_id, $table)
	{
		$id = $this->uri->segment($segment);
		$this->db->where($col_id, $id);
		$this->db->delete($table);
		return true;
	}

	// public function delete_percentage(){
	// 	$this->db->where('b_code',$_SESSION['aip']);
	// 	$this->db->where('fy',$_SESSION['fy']);
	// 	$this->db->where('school_id',$this->session->username);
	// 	$this->db->delete('sgod_app_percentage');
	// 	return true;
	// }


	public function table_num($table)
	{
		$query = $this->db->get_where($table, array('status' => '0'));
		return $query;
	}

	public function update_noti()
	{

		$id = $this->uri->segment(3);

		$data = array(
			'notify' => 0
		);

		$this->db->where('submit_id', $id);
		$this->db->where('res !=', $this->session->username);
		return $this->db->update('sgod_aip_track', $data);
	}



	//Settings area

	public function insert_app_cat()
	{
		$data = array(
			'category' => $this->input->post('category')
		);

		return $this->db->insert('sgod_settings_cat', $data);
	}

	public function update_app_cat()
	{

		$id = $this->input->post('id');

		$data = array(
			'category' => $this->input->post('category')
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_settings_cat', $data);
	}

	public function insert_app_pillar()
	{
		$data = array(
			'pillar' => $this->input->post('pillar')
		);

		return $this->db->insert('sgod_settings_pillar', $data);
	}

	public function update_app_pillar()
	{

		$id = $this->input->post('id');

		$data = array(
			'pillar' => $this->input->post('pillar')
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_settings_pillar', $data);
	}

	public function insert_domain()
	{
		$data = array(
			'domain' => $this->input->post('domain')
		);

		return $this->db->insert('sgod_settings_domain', $data);
	}

	public function update_domain()
	{

		$id = $this->input->post('id');

		$data = array(
			'domain' => $this->input->post('domain')
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_settings_domain', $data);
	}

	public function insert_strand()
	{
		$data = array(
			'strand' => $this->input->post('strand')
		);

		return $this->db->insert('sgod_settings_strand', $data);
	}

	public function insert_fy()
	{
		$data = array(
			'fy' => $this->input->post('fy')
		);

		return $this->db->insert('sgod_fy', $data);
	}

	public function update_strand()
	{

		$id = $this->input->post('id');

		$data = array(
			'strand' => $this->input->post('strand')
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_settings_strand', $data);
	}

	public function insert_pias()
	{
		$data = array(
			'pias' => $this->input->post('pias'),
			'year' => $this->input->post('year'),
			'school_id' => $this->session->username
		);

		return $this->db->insert('sgod_settings_pias', $data);
	}

	public function update_pias()
	{

		$id = $this->input->post('id');

		$data = array(
			'pias' => $this->input->post('pias'),
			'year' => $this->input->post('year')
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_settings_pias', $data);
	}

	public function insert_matatag()
	{
		$data = array(
			'matatag' => $this->input->post('matatag')
		);

		return $this->db->insert('sgod_settings_matatag', $data);
	}

	public function insert_app_percentage()
	{
		$data = array(
			'mb' => $this->input->post('mb'),
			'mr' => $this->input->post('mr'),
			'tli' => $this->input->post('tli'),
			'tst' => $this->input->post('tst'),
			'b_code' => $this->input->post('b_code'),
			'school_id' => $this->input->post('school_id'),
			'fy' => $_SESSION['fy']
		);

		return $this->db->insert('sgod_app_percentage', $data);
	}
	public function update_app_percentage()
	{
		$id = $this->input->post('id');

		$data = array(
			'mb' => $this->input->post('mb'),
			'mr' => $this->input->post('mr'),
			'tli' => $this->input->post('tli'),
			'tst' => $this->input->post('tst'),
			'b_code' => $this->input->post('b_code'),
			'school_id' => $this->input->post('school_id'),
			'fy' => $_SESSION['fy']
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_app_percentage', $data);
	}

	public function insert_bs()
	{
		$data = array(
			'description' => $this->input->post('description')
		);

		return $this->db->insert('sgod_settings_bs', $data);
	}

	public function update_matatag()
	{

		$id = $this->input->post('id');

		$data = array(
			'matatag' => $this->input->post('matatag')
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_settings_matatag', $data);
	}

	public function insert_aip()
	{
		$g = $this->input->post('group');
		if ($g == 4) {
			$bs = "SNED Fund";
		} else {
			$bs = $this->input->post('budget_source');
		}

		$data = array(
			'school_id' => $this->input->post('school_id'),
			'fy' => $this->input->post('fy'),
			'pillar' => $this->input->post('pillar'),
			'domain' => $this->input->post('domain'),
			'strand' => $this->input->post('strand'),
			'pia' => $this->input->post('pia'),
			'sip_project' => $this->input->post('sip_project'),
			'sip_pObjective' => $this->input->post('sip_pObjective'),
			'sip_output' => $this->input->post('sip_output'),
			'strategy' => $this->input->post('strategy'),
			'pi' => $this->input->post('pi'),
			'movs' => $this->input->post('movs'),
			'pr' => $this->input->post('pr'),
			'schedule' => $this->input->post('schedule'),
			'venue' => $this->input->post('venue'),
			'budget' => $this->input->post('budget'),
			'budget_source' => $bs,
			'materials' => $this->input->post('materials'),
			'matatag' => $this->input->post('matatag'),
			'b_code' => $this->input->post('b_code'),
			'category' => $this->input->post('category'),
			'group' => $this->input->post('group'),
			'io' => $this->input->post('io'),
			'procurement' => $this->input->post('procurement')
		);

		return $this->db->insert('sgod_aip', $data);
	}

	public function insert_app()
	{

		$materials = explode(',', $this->input->post('materials'));
		$id = $this->db->insert_id();

		for ($i = 0; $i < count($materials); $i++) {

			$item = array(
				'materials' => $materials[$i],
				'aip_id' => $id,
				'b_code' => $this->input->post('b_code'),
				'fy' => $this->input->post('fy'),
				'school_id' => $this->session->username,
				'bs' => $this->input->post('budget_source')
			);

			$this->db->insert('sgod_app', $item);
		}
	}

	public function update_app()
	{

		$materials = explode(',', $this->input->post('materials'));
		$id = $this->input->post('aip_id');

		for ($i = 0; $i < count($materials); $i++) {

			$item = array(
				'materials' => $materials[$i],
				'aip_id' => $id,
				'b_code' => $this->input->post('b_code'),
				'fy' => $this->input->post('fy'),
				'school_id' => $this->session->username,
				'bs' => $this->input->post('budget_source')
			);

			$this->db->insert('sgod_app', $item);
		}
	}

	public function reupdate_app()
	{

		$id = $this->input->post('id');
		$data = array(
			'unit_price' => $this->input->post('unit_price'),
			'quantity' => 0,
			'unit_measure' => $this->input->post('unit_measure'),
			'budget_alloc' => 0,
			'jan' => $this->input->post('jan'),
			'feb' => $this->input->post('feb'),
			'mar' => $this->input->post('mar'),
			'april' => $this->input->post('april'),
			'may' => $this->input->post('may'),
			'june' => $this->input->post('june'),
			'july' => $this->input->post('july'),
			'aug' => $this->input->post('aug'),
			'sept' => $this->input->post('sept'),
			'oct' => $this->input->post('oct'),
			'nov' => $this->input->post('nov'),
			'ddec' => $this->input->post('dec'),
			'stat' => 1,
			'qjan' => $this->input->post('qjan'),
			'qfeb' => $this->input->post('qfeb'),
			'qmar' => $this->input->post('qmar'),
			'qapril' => $this->input->post('qapril'),
			'qmay' => $this->input->post('qmay'),
			'qjune' => $this->input->post('qjune'),
			'qjuly' => $this->input->post('qjuly'),
			'qaug' => $this->input->post('qaug'),
			'qsept' => $this->input->post('qsept'),
			'qoct' => $this->input->post('qoct'),
			'qnov' => $this->input->post('qnov'),
			'qdec' => $this->input->post('qdec'),
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_app', $data);
	}

	public function insert_sop()
	{
		$data = array(
			'aip_id' => $this->input->post('aip_id'),
			'q1' => $this->input->post('q1'),
			'q2' => $this->input->post('q2'),
			'q3' => $this->input->post('q3'),
			'q4' => $this->input->post('q4'),
			'total' => $this->input->post('total'),
			'type' => $this->input->post('type')
		);

		return $this->db->insert('sgod_sop', $data);
	}
	public function update_sop($id)
	{
		$data = array(
			'q1' => $this->input->post('q1'),
			'q2' => $this->input->post('q2'),
			'q3' => $this->input->post('q3'),
			'q4' => $this->input->post('q4'),
			'total' => $this->input->post('total'),
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_sop', $data);
	}

	public function update_aip($param)
	{
		$g = $this->input->post('group');
		if ($g == 4) {
			$bs = "SNED Fund";
		} else {
			$bs = $this->input->post('budget_source');
		}

		$data = array(
			'school_id' => $this->input->post('school_id'),
			'fy' => $this->input->post('fy'),
			'pillar' => $this->input->post('pillar'),
			'domain' => $this->input->post('domain'),
			'strand' => $this->input->post('strand'),
			'pia' => $this->input->post('pia'),
			'sip_project' => $this->input->post('sip_project'),
			'sip_pObjective' => $this->input->post('sip_pObjective'),
			'sip_output' => $this->input->post('sip_output'),
			'strategy' => $this->input->post('strategy'),
			'pi' => $this->input->post('pi'),
			'movs' => $this->input->post('movs'),
			'pr' => $this->input->post('pr'),
			'schedule' => $this->input->post('schedule'),
			'venue' => $this->input->post('venue'),
			'category' => $this->input->post('category'),
			'budget' => $this->input->post('budget'),
			'budget_source' => $bs,
			'matatag' => $this->input->post('matatag'),
			'io' => $this->input->post('io'),
			'procurement' => $this->input->post('procurement')
		);

		$this->db->where('id', $param);
		return $this->db->update('sgod_aip', $data);
	}



	public function insert_aip_action()
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $this->input->post('submit_id'),
			'action' => $this->input->post('action'),
			'remarks' => $this->input->post('remarks'),
			'tdate' => $date,
			'ttime' => $t,
			'res' => $this->session->username
		);

		return $this->db->insert('sgod_aip_track', $data);
	}

	public function insert_materials()
	{

		$data = array(
			'aip_id' => $this->input->post('aip_id'),
			'materials' => $this->input->post('material'),
			'aip_id' => $this->input->post('aip_id'),
			'school_id' => $this->input->post('school_id'),
			'fy' => $this->input->post('fy'),
			'b_code' => $this->input->post('b_code')
		);

		return $this->db->insert('sgod_app', $data);
	}

	public function update_aip_materials()
	{

		$aip_id = $this->input->post('aip_id');
		$mat = $this->input->post('material');
		$mats = $this->input->post('aip_marterials');

		$data = array(
			'materials' => $mats . ', ' . $mat
		);

		$this->db->where('id', $aip_id);
		return $this->db->update('sgod_aip', $data);
	}

	public function update_aip_material($mat)
	{

		$aip_id = $this->uri->segment(4);

		$data = array(
			'materials' => $mat
		);

		$this->db->where('id', $aip_id);
		return $this->db->update('sgod_aip', $data);
	}

	public function aip_approved()
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $this->uri->segment(3),
			'remarks' => "Approved",
			'tdate' => $date,
			'dtime' => $t,
			'school_id' => $this->uri->segment(4),
			'res' => $this->session->username
		);

		return $this->db->insert('sgod_aip_track', $data);
	}
	public function aip_approved_sned()
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $this->uri->segment(3),
			'remarks' => "SNED Approved",
			'tdate' => $date,
			'dtime' => $t,
			'school_id' => $this->uri->segment(4),
			'res' => $this->session->username
		);

		return $this->db->insert('sgod_aip_track', $data);
	}

	public function aip_review_track($remarks)
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $this->uri->segment(3),
			'remarks' => $remarks,
			'tdate' => $date,
			'dtime' => $t,
			'school_id' => $this->uri->segment(4),
			'res' => $this->session->username
		);

		return $this->db->insert('sgod_aip_track', $data);
	}

	public function update_aip_action_sned()
	{

		$id = $this->uri->segment(3);

		$data = array(
			'status' => 0,
			'remarks' => "SNED Approved"
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_aip_submit', $data);
	}

	public function update_aip_action_review($status,$remarks)
	{

		$id = $this->uri->segment(3);

		$data = array(
			'status' => $status,
			'remarks' => $remarks
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_aip_submit', $data);
	}

	public function request()
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'fy' => $_SESSION['fy'],
			'b_code' => $_SESSION['aip'],
			'school_id' => $this->session->username,
			'tdate' => $date,
			'ttime' => $t,
			'remarks' => $this->input->post('remarks'),
			's_id' => $this->input->post('id')
		);

		return $this->db->insert('sgod_aip_request', $data);
	}
	public function request_update()
	{
		$data = array(
			'stat' => 1
		);

		$this->db->where('id', $this->input->post('r_id'));
		return $this->db->update('sgod_aip_request', $data);
	}

	public function request_insert_track()
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $this->input->post('id'),
			'remarks' => 'Request for Unlock: ' . $this->input->post('remarks'),
			'tdate' => $date,
			'dtime' => $t,
			'school_id' => $this->input->post('school_id'),
			'res' => $this->session->username
		);

		return $this->db->insert('sgod_aip_track', $data);
	}

	public function aip_remarks()
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $this->input->post('id'),
			'school_id' => $this->input->post('school_id'),
			'remarks' => $this->input->post('remarks'),
			'tdate' => $date,
			'dtime' => $t,
			'res' => $this->session->username,
			'notify' => 1,
			'position' => $this->session->position
		);

		return $this->db->insert('sgod_aip_track', $data);
	}

	public function aip_open()
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $this->input->post('id'),
			'remarks' => $this->input->post('remarks'),
			'tdate' => $date,
			'dtime' => $t,
			'school_id' => $this->input->post('school_id'),
			'res' => $this->session->username
		);
		$this->db->where('id', $this->input->post('id'));
		return $this->db->insert('sgod_aip_track', $data);
	}
	public function update_aip_open()
	{

		$id = $this->input->post('id');

		// sgod_aip_submit.remarks carries the stage label ("Submitted", "AIP Reviewed",
		// ...), not free text - the approver's reason is kept on the sgod_aip_track row
		// aip_open() writes. This used to read post('reason'), a field no form posts, so
		// every unlocked plan ended up with a blank stage label.
		$data = array(
			'status' => 0,
			'remarks' => 'Unlocked for Editing'
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_aip_submit', $data);
	}

	public function update_aip_action()
	{

		$id = $this->uri->segment(3);

		$data = array(
			'status' => 1,
			'remarks' => "Approved"
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_aip_submit', $data);
	}

	public function aip_submit($fy, $id, $bcode)
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());

		$data = array(
			'school_id' => $id,
			'fy' => $fy,
			'remarks' => 'Submitted',
			'res' => $this->session->username,
			'date' => $date,
			'b_code' => $bcode
		);

		return $this->db->insert('sgod_aip_submit', $data);
	}

	public function aip_submit_sned($fy, $id, $bcode)
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());

		$data = array(
			'school_id' => $id,
			'fy' => $fy,
			'remarks' => 'Submitted',
			'res' => $this->session->username,
			'date' => $date,
			'b_code' => $bcode,
			'status' => 2
		);

		return $this->db->insert('sgod_aip_submit', $data);
	}

	public function aip_submit_sbfp($fy, $id, $bcode)
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());

		$data = array(
			'school_id' => $id,
			'fy' => $fy,
			'remarks' => 'Submitted',
			'res' => $this->session->username,
			'date' => $date,
			'b_code' => $bcode,
			'status' => 6
		);

		return $this->db->insert('sgod_aip_submit', $data);
	}

	public function aip_track($id)
	{
		date_default_timezone_set('Asia/Manila');
		$date = date('Y-m-d', time());
		$t = date('h:i:s a', time());

		$data = array(
			'submit_id' => $id,
			'school_id' => $this->session->username,
			'remarks' => 'Submitted',
			'res' => $this->session->username,
			'tdate' => $date,
			'dtime' => $t,
			'position' => 'School',
			'notify' => 1
		);

		return $this->db->insert('sgod_aip_track', $data);
	}

	// public function update_aip_status(){

	// 	$fy = 2023;
	// 	$school_id = 129443;

	// 	$data = array(
	// 	'status' => 0
	// 	); 

	// 	//$this->db->where('status', 0);
	// 	$this->db->where('fy', $fy);
	// 	$this->db->where('school_id', $school_id);
	// 	return $this->db->update('sgod_aip', $data);	
	// }


	function schools($type)
	{
		$this->db->where('schoolType', $type);
		$this->db->order_by('schoolName');
		$result = $this->db->get('schools');
		return $result->result();
	}
	public function insert_memo()
	{
		$file = $this->upload->data();
		$filename = $file['file_name'];

		$data = array(
			'fileName' => $filename,
			'title' => $this->input->post('title'),
			'memoNo' => $this->input->post('memoNo'),
			'added_by' => $this->session->username
		);

		return $this->db->insert('sgod_memo', $data);
	}

	public function memo_update()
	{

		$data = array(
			'title' => $this->input->post('title'),
			'memoNo' => $this->input->post('memoNo'),
			'added_by' => $this->session->username
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_memo', $data);
	}

	public function mfu(){
		$file = $this->upload->data();
        $filename = $file['file_name'];

        $data = array(
            'fileName' => $filename,
			'added_by' => $this->session->username
        ); 
		
		$this->db->where('id', $this->input->post('id'));
        return $this->db->update('sgod_memo', $data);
    }
	

	public function multiple_images($image = array())
	{
		return $this->db->insert_batch('sgod_acc_image', $image);
	}

	public function atr($image = array())
	{
		return $this->db->insert_batch('sgod_files', $image);
	}

	function delete_group($param, $attach, $path, $table)
	{
		$this->db->where('id', $param);
		unlink("upload/" . $path . "/" . $attach);
		$this->db->delete($table, array('id' => $param));
	}

	function copy_row($param)
	{
		$query = $this->db->query("INSERT INTO sgod_accomplishments
		(quarter, year, monthAcc, weekAcc, section, activity, activityCategory, particulars, venue, targetDate, dateConducted, encoder, resources, notes, perIndicators, target, achieved, percentageAccom, remarks, secGroup)
		SELECT quarter, year, monthAcc, weekAcc, section, activity, activityCategory, particulars, venue, targetDate, dateConducted, encoder, resources, notes, perIndicators, target, achieved, percentageAccom, remarks, secGroup
		FROM sgod_accomplishments
		WHERE id = '{$param}'");
	}

	public function get_accomplishment_by_date($year, $month, $week, $section, $secGroup)
	{
		$this->db->where("year", $year);
		$this->db->where("monthAcc", $month);
		$this->db->where("weekAcc", $week);
		$this->db->where('section', $section);
		$this->db->where('secGroup', $secGroup);
		$result = $this->db->get('sgod_accomplishments');

		return $result->result();
	}

	public function two_cond_count_rows($table, $col, $val, $col2, $val2)
	{
		$result = $this->db->where($col, $val);
		$result = $this->db->where($col2, $val2);
		$result = $this->db->get($table);
		return $result;
	}

	public function one_cond_count_rows($table, $col, $val)
	{
		$result = $this->db->where($col, $val);
		$result = $this->db->get($table);
		return $result;
	}



	// code @ 1/12/2024 //

	public function insert_school()
	{
		$data = array(
			'schoolID' => $this->input->post('schoolID'),
			'schoolName' => $this->input->post('schoolName'),
			'division' => 'Davao Oriental Division',
			'district' => $this->input->post('district'),
			'course' => $this->input->post('course'),
			'schoolType' => $this->input->post('schoolType'),
			'yearEstab' => $this->input->post('yearEstab'),
			'schoolEmail' => $this->input->post('schoolEmail'),
			'congDist' => $this->input->post('congDist'),
			'province' => $this->input->post('province'),
			'city' => $this->input->post('city'),
			'brgy' => $this->input->post('brgy'),
			'adminFName' => $this->input->post('adminFName'),
			'adminMName' => $this->input->post('adminMName'),
			'adminLName' => $this->input->post('adminLName'),
			'adminMobile' => $this->input->post('adminMobile'),
			'adminEmail' => $this->input->post('adminEmail'),
			'settingsID' => 1,
			'adminDesignation' => $this->input->post('adminDesignation'),
			'schoolLogo' => 'logo.png'
		);

		return $this->db->insert('schools', $data);
	}

	public function update_fund_allocation()
	{

		$id = $this->input->post('id');
		$fund = $this->input->post('alloc_amount');
		$m = $fund / 12;

		$data = array(
			'alloc_amount' => $fund,
			// 'alloc_group' => $this->input->post('group'),
			// 'alloc_type' => $this->input->post('type'),
			// 'alloc_year' => $this->input->post('fy'),
			'mo_jan' => $m,
			'mo_feb' => $m,
			'mo_mar' => $m,
			'mo_apr' => $m,
			'mo_may' => $m,
			'mo_jun' => $m,
			'mo_jul' => $m,
			'mo_aug' => $m,
			'mo_sep' => $m,
			'mo_oct' => $m,
			'mo_nov' => $m,
			'mo_dec' => $m
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_school_allocation', $data);
	}

	public function change_pass()
	{

		$id = $this->input->post('id');

		$password = $this->input->post('pass');
		$hash = password_hash($password, PASSWORD_DEFAULT);
		$data = array(
			'password' => $hash
		);

		$this->db->where('id', $id);
		return $this->db->update('users', $data);
	}

	public function insert_fund_allocation()
	{
		$fund = str_replace(',', '', $this->input->post('alloc_amount'));
		$f = $fund / 12;

		if ($this->input->post('type') == 'MOOE') {
			$jan = $f;
			$feb = $f;
			$mar = $f;
			$apr = $f;
			$may = $f;
			$jun = $f;
			$jul = $f;
			$aug = $f;
			$sep = $f;
			$oct = $f;
			$nov = $f;
			$dec = $f;
		} else {
			$jan = 0;
			$feb = 0;
			$mar = 0;
			$apr = 0;
			$may = 0;
			$jun = 0;
			$jul = 0;
			$aug = 0;
			$sep = 0;
			$oct = 0;
			$nov = 0;
			$dec = 0;
		}


		$data = array(
			'schoolID' => $this->input->post('schoolID'),
			'alloc_year' => $this->input->post('fy'),
			'alloc_batch' => $this->input->post('bcode'),
			'alloc_amount' => $fund,
			'alloc_type' => $this->input->post('type'),
			'alloc_group' => $this->input->post('group'),
			'mo_jan' => $jan,
			'mo_feb' => $feb,
			'mo_mar' => $mar,
			'mo_apr' => $apr,
			'mo_may' => $may,
			'mo_jun' => $jun,
			'mo_jul' => $jul,
			'mo_aug' => $aug,
			'mo_sep' => $sep,
			'mo_oct' => $oct,
			'mo_nov' => $nov,
			'mo_dec' => $dec
		);

		return $this->db->insert('sgod_school_allocation', $data);
	}

	public function sbm_insert()
	{
		$data = array(
			'indicator' => $this->input->post('indicator'),
			'description' => $this->input->post('description')
		);

		return $this->db->insert('sbm_indicator', $data);
	}

	public function sbm_update()
	{
		$data = array(
			'indicator' => $this->input->post('indicator'),
			'description' => $this->input->post('description')
		);


		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbm_indicator', $data);
	}

	public function sbm_sub_insert()
	{

		$data = array(
			'priciple_id' => $this->input->post('priciple_id'),
			'i_no' => $this->input->post('i_no'),
			'description' => $this->input->post('description')
		);

		return $this->db->insert('sbm_sub_indicator', $data);
	}

	public function sbm_sub_update()
	{

		$data = array(
			'priciple_id' => $this->input->post('priciple_id'),
			'i_no' => $this->input->post('i_no'),
			//'i_status' => $this->input->post('i_status'), 
			'description' => $this->input->post('description')
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbm_sub_indicator', $data);
	}

	public function sbm_cecklist_insert()
	{
		$data = array(
			'q1' => $this->input->post('q1'),
			'q2' => $this->input->post('q2'),
			'q3' => $this->input->post('q3'),
			'q4' => $this->input->post('q4'),
			'q5' => $this->input->post('q5'),
			'q6' => $this->input->post('q6'),
			'q7' => $this->input->post('q7'),
			'q8' => $this->input->post('q8'),
			'q9' => $this->input->post('q9'),
			'q10' => $this->input->post('q10'),
			'q11' => $this->input->post('q11'),
			'q12' => $this->input->post('q12'),
			'q13' => $this->input->post('q13'),
			'q14' => $this->input->post('q14'),
			'q15' => $this->input->post('q15'),
			'q16' => $this->input->post('q16'),
			'q17' => $this->input->post('q17'),
			'q18' => $this->input->post('q18'),
			'q19' => $this->input->post('q19'),
			'q20' => $this->input->post('q20'),
			'q21' => $this->input->post('q21'),
			'q22' => $this->input->post('q22'),
			'q23' => $this->input->post('q23'),
			'q24' => $this->input->post('q24'),
			'q25' => $this->input->post('q25'),
			'q26' => $this->input->post('q26'),
			'q27' => $this->input->post('q27'),
			'q28' => $this->input->post('q28'),
			'q29' => $this->input->post('q29'),
			'q30' => $this->input->post('q30'),
			'q31' => $this->input->post('q31'),
			'q32' => $this->input->post('q32'),
			'q33' => $this->input->post('q33'),
			'q34' => $this->input->post('q34'),
			'q35' => $this->input->post('q35'),
			'q36' => $this->input->post('q36'),
			'q37' => $this->input->post('q37'),
			'q38' => $this->input->post('q38'),
			'q39' => $this->input->post('q39'),
			'q40' => $this->input->post('q40'),
			'q41' => $this->input->post('q41'),
			'q42' => $this->input->post('q42'),
			'school_id' => $this->session->username,
			'fy' => $_SESSION['sbm_fy'],
			'district' => $this->input->post('district')

		);


		return $this->db->insert('sbm', $data);
	}

	public function sbm_ta_insert()
	{
		$data = [];

		// Collect data for 'q', 'qq', 'a', and 'f' fields
		foreach (['q', 'qq', 'a', 'f'] as $prefix) {
			for ($i = 1; $i <= 42; $i++) {
				$data["{$prefix}{$i}"] = $this->input->post("{$prefix}{$i}");
			}
		}

		// Add additional fields
		$data['school_id'] = $this->session->username;
		$data['fy'] = $_SESSION['sbm_fy'];
		$data['district'] = $this->input->post('district');

		return $this->db->insert('sbm_ta', $data);
	}

	public function sbm_ta_update()
	{
		$data = [];

		// Collect data for 'q', 'qq', 'a', and 'f' fields
		foreach (['q', 'qq', 'a', 'f'] as $prefix) {
			for ($i = 1; $i <= 42; $i++) {
				$data["{$prefix}{$i}"] = $this->input->post("{$prefix}{$i}");
			}
		}

		// Add additional fields
		//$data['school_id'] = $this->session->username;
		//$data['fy'] = date('Y');
		$data['district'] = $this->input->post('district');

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbm_ta', $data);
	}

	public function sbm_cecklist_update()
	{
		$data = array(
			'q1' => $this->input->post('q1'),
			'q2' => $this->input->post('q2'),
			'q3' => $this->input->post('q3'),
			'q4' => $this->input->post('q4'),
			'q5' => $this->input->post('q5'),
			'q6' => $this->input->post('q6'),
			'q7' => $this->input->post('q7'),
			'q8' => $this->input->post('q8'),
			'q9' => $this->input->post('q9'),
			'q10' => $this->input->post('q10'),
			'q11' => $this->input->post('q11'),
			'q12' => $this->input->post('q12'),
			'q13' => $this->input->post('q13'),
			'q14' => $this->input->post('q14'),
			'q15' => $this->input->post('q15'),
			'q16' => $this->input->post('q16'),
			'q17' => $this->input->post('q17'),
			'q18' => $this->input->post('q18'),
			'q19' => $this->input->post('q19'),
			'q20' => $this->input->post('q20'),
			'q21' => $this->input->post('q21'),
			'q22' => $this->input->post('q22'),
			'q23' => $this->input->post('q23'),
			'q24' => $this->input->post('q24'),
			'q25' => $this->input->post('q25'),
			'q26' => $this->input->post('q26'),
			'q27' => $this->input->post('q27'),
			'q28' => $this->input->post('q28'),
			'q29' => $this->input->post('q29'),
			'q30' => $this->input->post('q30'),
			'q31' => $this->input->post('q31'),
			'q32' => $this->input->post('q32'),
			'q33' => $this->input->post('q33'),
			'q34' => $this->input->post('q34'),
			'q35' => $this->input->post('q35'),
			'q36' => $this->input->post('q36'),
			'q37' => $this->input->post('q37'),
			'q38' => $this->input->post('q38'),
			'q39' => $this->input->post('q39'),
			'q40' => $this->input->post('q40'),
			'q41' => $this->input->post('q41'),
			'q42' => $this->input->post('q42'),
			//'school_id' => $this->session->username,
			'district' => $this->input->post('district')

		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbm', $data);
	}

	public function sbm_cecklist_district_insert()
	{
		$data = [];

		// Collect data for questions
		for ($i = 1; $i <= 42; $i++) {
			$data["q$i"] = $this->input->post("r$i");
		}

		// Collect data for forms
		for ($i = 1; $i <= 42; $i++) {
			$data["fs$i"] = $this->input->post("fs$i");
		}

		// Additional data
		$data['school_id'] = $this->input->post('school_id');
		$data['fy'] = date('Y');

		return $this->db->insert('sbm_remark', $data);
	}

	public function sbm_cecklist_admin_insert()
	{
		$data = [];

		// Collect data for questions
		for ($i = 1; $i <= 42; $i++) {
			$data["q$i"] = $this->input->post("r$i");
		}

		// Collect data for forms
		for ($i = 1; $i <= 42; $i++) {
			$data["fs$i"] = $this->input->post("fs$i");
		}

		// Additional data
		$data['school_id'] = $this->input->post('school_id');
		$data['fy'] = date('Y');

		return $this->db->insert('sbm_remark_admin', $data);
	}


	public function sbm_checklist_district_update()
	{
		$data = [];

		// Collect data for questions
		for ($i = 1; $i <= 42; $i++) {
			$data["q$i"] = $this->input->post("r$i");
		}

		// Collect data for forms
		for ($i = 1; $i <= 42; $i++) {
			$data["fs$i"] = $this->input->post("fs$i");
		}


		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbm_remark', $data);
	}

	public function sbm_checklist_admin_update()
	{
		$data = [];

		// Collect data for questions
		for ($i = 1; $i <= 42; $i++) {
			$data["q$i"] = $this->input->post("r$i");
		}

		// Collect data for forms
		for ($i = 1; $i <= 42; $i++) {
			$data["fs$i"] = $this->input->post("fs$i");
		}


		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbm_remark_admin', $data);
	}

	public function get_total_value($col1, $col2, $bcode)
	{
		$this->db->select('SUM(' . $col1 . ' * ' . $col2 . ') AS total');
		$this->db->where('school_id', $this->session->username);
		$this->db->where('fy', '2024');
		$this->db->where('b_code', $bcode);
		$query = $this->db->get('sgod_app');

		if ($query->num_rows() > 0) {
			return $query->row()->total;
		}
		return 0; // Return 0 if no records found
	}


	public function show_rca()
	{
		$this->db->select('a.id, a.category, b.aip_id, b.school_id, b.materials, b.qjan, b.jan');
		$this->db->from('sgod_aip as a');
		$this->db->join('sgod_app as b', 'a.id = b.aip_id', 'left');
		$this->db->where('b.school_id', '129463');
		$this->db->where('b.qjan !=', 0);
		$query = $this->db->get();
		return $query->result();
	}

	public function insert_rca($month, $qmonth, $cm, $qcm, $bcode)
	{
		$this->db->select('a.id, a.category, b.aip_id, b.school_id, b.materials, b.unit_measure,' . $qmonth . ', ' . $month . ', b.b_code, b.fy');
		$this->db->from('sgod_aip as a');
		$this->db->join('sgod_app as b', 'a.id = b.aip_id', 'left');
		$this->db->where('b.school_id', $this->session->username);
		$this->db->where('b.b_code', $bcode);
		$this->db->where('b.' . $qcm . ' !=', 0);
		$query = $this->db->get();

		// Fetch the results
		$results = $query->result_array();

		// Prepare the data for insertion
		$data_to_insert = [];
		foreach ($results as $row) {
			$data_to_insert[] = [
				'category' => $row['category'],
				'item_des' => $row['materials'],
				'unit_mesure' => $row['unit_measure'],
				'cost' => $row[$cm],
				'qty' => $row[$qcm],
				'bcode' => $row['b_code'],
				'fy' => $row['fy'],
				'school_id' => $row['school_id'],
				'mm' => $cm,
				'aip_id' => $row['aip_id']
			];
		}

		// Insert data into the destination table
		if (!empty($data_to_insert)) {
			$this->db->insert_batch('sgod_liquidation', $data_to_insert);
		}

		return $results; // You can return results or any other indication of success
	}

	public function liq_update()
	{

		$data = array(
			'category' => $this->input->post('category'),
			'item_des' => $this->input->post('item_des'),
			'unit_mesure' => $this->input->post('unit_mesure'),
			'cost' => $this->input->post('unit_cost'),
			'qty' => $this->input->post('quantity'),
			'acc_name' => $this->input->post('acc_name'),


		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_liquidation', $data);
	}

	public function action_plan_insert()
	{

		$data = array(
			'activity' => $this->input->post('activity'),
			'objective' => $this->input->post('objective'),
			'ex_output' => $this->input->post('ex_output'),
			'metho_strategy' => $this->input->post('metho_strategy'),
			'time_frame' => $this->input->post('time_frame'),
			'person_involved' => $this->input->post('person_involved'),
			'bud_req' => $this->input->post('bud_req'),
			'remarks' => $this->input->post('remarks'),
			'fy' => $_SESSION['sbm_fy'],
			'school_id' => $this->session->username

		);

		return $this->db->insert('sgod_action_plan', $data);
	}

	public function sbm_tech_insert()
	{

		$data = array(
			'ta_rec' => $this->input->post('ta_rec'),
			'sa' => $this->input->post('sa'),
			'cd' => $this->input->post('cd'),
			'mtd' => $this->input->post('mtd'),
			'schedule' => $this->input->post('schedule'),
			'ct' => $this->input->post('ct'),
			'district' => $this->session->c_id,
			'fy' => date('Y'),

		);

		return $this->db->insert('sbm_tech', $data);
	}

	public function sbm_tech_update()
	{

		$data = array(
			'ta_rec' => $this->input->post('ta_rec'),
			'sa' => $this->input->post('sa'),
			'cd' => $this->input->post('cd'),
			'mtd' => $this->input->post('mtd'),
			'schedule' => $this->input->post('schedule'),
			'ct' => $this->input->post('ct')

		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbm_tech', $data);
	}

	public function tapr_insert()
	{

		$data = array(
			'indicator_id' => $this->input->post('indicator_id'),
			'dm' => $this->input->post('dm'),
			'cigpb' => $this->input->post('cigpb'),
			'cat' => $this->input->post('cat'),
			'prc' => $this->input->post('prc'),
			'fy' => date('Y'),
			'school_id' => $this->session->username

		);

		return $this->db->insert('sgod_sbm_tapr', $data);
	}

	public function tapr_update()
	{

		$data = array(
			'indicator_id' => $this->input->post('indicator_id'),
			'dm' => $this->input->post('dm'),
			'cigpb' => $this->input->post('cigpb'),
			'cat' => $this->input->post('cat'),
			'prc' => $this->input->post('prc'),
			'fy' => date('Y'),
			'school_id' => $this->session->username

		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_sbm_tapr', $data);
	}

	public function action_plan_update()
	{

		$data = array(
			'activity' => $this->input->post('activity'),
			'objective' => $this->input->post('objective'),
			'ex_output' => $this->input->post('ex_output'),
			'metho_strategy' => $this->input->post('metho_strategy'),
			'time_frame' => $this->input->post('time_frame'),
			'person_involved' => $this->input->post('person_involved'),
			'bud_req' => $this->input->post('bud_req'),
			'remarks' => $this->input->post('remarks'),

		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_action_plan', $data);
	}

	public function sbcp_insert()
	{
		$data = array(
			'school_id' => $this->input->post('school_id'),
			'fy' => $this->input->post('fy'),
			'cdate' => $this->input->post('cdate'),
			'district' => $this->input->post('district'),
			'q1a1' => $this->input->post('q1a1'),
			'q1a2' => $this->input->post('q1a2'),
			'q1a3' => $this->input->post('q1a3'),
			'q1a4' => $this->input->post('q1a4'),
			'q1a5' => $this->input->post('q1a5'),
			'q1a6' => $this->input->post('q1a6'),
			'q1a7' => $this->input->post('q1a7'),
			'q1a8' => $this->input->post('q1a8'),
			'q1a9' => $this->input->post('q1a9'),
			'q1a10' => $this->input->post('q1a10'),
			'q1a11' => $this->input->post('q1a11'),
			'q1a12' => $this->input->post('q1a12'),
			'q1a13' => $this->input->post('q1a13'),
			'q1a14' => $this->input->post('q1a14'),
			'q1a15' => $this->input->post('q1a15'),
			'q1a16' => $this->input->post('q1a16'),
			'q1a17' => $this->input->post('q1a17'),
			'q1a18' => $this->input->post('q1a18'),
			'q1a19' => $this->input->post('q1a19'),
			'q1a20' => $this->input->post('q1a20'),
			'q1a21' => $this->input->post('q1a21'),
			'q1b1' => $this->input->post('q1b1'),
			'q1b2' => $this->input->post('q1b2'),
			'q1b3' => $this->input->post('q1b3'),
			'q1b4' => $this->input->post('q1b4'),
			'q1b5' => $this->input->post('q1b5'),
			'q1b6' => $this->input->post('q1b6'),
			'q1b7' => $this->input->post('q1b7'),
			'q1b8' => $this->input->post('q1b8'),
			'q1b9' => $this->input->post('q1b9'),
			'q2a1' => $this->input->post('q2a1'),
			'q2a2' => $this->input->post('q2a2'),
			'q2a3' => $this->input->post('q2a3'),
			'q2a4' => $this->input->post('q2a4'),
			'q2a5' => $this->input->post('q2a5'),
			'q2a6' => $this->input->post('q2a6'),
			'q2a7' => $this->input->post('q2a7'),
			'q2a8' => $this->input->post('q2a8'),
			'q2a9' => $this->input->post('q2a9'),
			'q2a10' => $this->input->post('q2a10'),
			'q2a11' => $this->input->post('q2a11'),
			'q2a12' => $this->input->post('q2a12'),
			'q2a13' => $this->input->post('q2a13'),
			'q2a14' => $this->input->post('q2a14'),
			'q3a1' => $this->input->post('q3a1'),
			'q3a2' => $this->input->post('q3a2'),
			'q3a3' => $this->input->post('q3a3'),
			'q3a4' => $this->input->post('q3a4'),
			'q3a5' => $this->input->post('q3a5'),
			'q3a6' => $this->input->post('q3a6'),
			'q3a7' => $this->input->post('q3a7'),
			'q3a8' => $this->input->post('q3a8'),
			'q3a9' => $this->input->post('q3a9'),
			'q3a10' => $this->input->post('q3a10'),
			'q3b1' => $this->input->post('q3b1'),
			'q3b2' => $this->input->post('q3b2'),
			'q3b3' => $this->input->post('q3b3'),
			'q3b4' => $this->input->post('q3b4'),
			'q3b5' => $this->input->post('q3b5'),
			'q3b6' => $this->input->post('q3b6'),
			'q3b7' => $this->input->post('q3b7'),
			'q3b8' => $this->input->post('q3b8'),
			'q3b9' => $this->input->post('q3b9'),
			'q3b10' => $this->input->post('q3b10'),
			'q3b11' => $this->input->post('q3b11'),
			'q3b12' => $this->input->post('q3b12'),
			'q3b13' => $this->input->post('q3b13'),
			'q3b14' => $this->input->post('q3b14'),
			'q4a1' => $this->input->post('q4a1'),
			'q4a2' => $this->input->post('q4a2'),
			'q4a3' => $this->input->post('q4a3'),
			'q4a4' => $this->input->post('q4a4'),
			'q4a5' => $this->input->post('q4a5'),
			'q4a6' => $this->input->post('q4a6'),
			'q4a7' => $this->input->post('q4a7'),
			'q4a8' => $this->input->post('q4a8'),
			'q4a9' => $this->input->post('q4a9'),
			'q4a10' => $this->input->post('q4a10'),
			'q4b1' => $this->input->post('q4b1'),
			'q4b2' => $this->input->post('q4b2'),
			'q4b3' => $this->input->post('q4b3'),
			'q4b4' => $this->input->post('q4b4'),
			'q4b5' => $this->input->post('q4b5'),
			'q4b6' => $this->input->post('q4b6'),
			'q4b7' => $this->input->post('q4b7'),
			'q4c1' => $this->input->post('q4c1'),
			'q4c2' => $this->input->post('q4c2'),
			'q4c3' => $this->input->post('q4c3'),
			'q4c4' => $this->input->post('q4c4'),
			'q4c5' => $this->input->post('q4c5'),
			'q4c6' => $this->input->post('q4c6'),
			'q4c7' => $this->input->post('q4c7'),
			'q4c8' => $this->input->post('q4c8'),
			'q4c9' => $this->input->post('q4c9'),
			'q4c10' => $this->input->post('q4c10'),
			'q4c11' => $this->input->post('q4c11'),
			'q4c12' => $this->input->post('q4c12'),
			'q4c13' => $this->input->post('q4c13'),
			'q4c14' => $this->input->post('q4c14'),
			'q4c15' => $this->input->post('q4c15'),
			'q4d1' => $this->input->post('q4d1'),
			'q4d2' => $this->input->post('q4d2'),
			'q4d3' => $this->input->post('q4d3'),
			'q4d4' => $this->input->post('q4d4'),
			'q4d5' => $this->input->post('q4d5'),
			'q4d6' => $this->input->post('q4d6'),
			'q4d7' => $this->input->post('q4d7'),
			'q5a1' => $this->input->post('q5a1'),
			'q5a2' => $this->input->post('q5a2'),
			'q5a3' => $this->input->post('q5a3'),
			'q5a4' => $this->input->post('q5a4'),
			'q5a5' => $this->input->post('q5a5'),
			'q5a6' => $this->input->post('q5a6'),
			'q5a7' => $this->input->post('q5a7'),
			'q5a8' => $this->input->post('q5a8'),
			'q5a9' => $this->input->post('q5a9'),
			'q5a10' => $this->input->post('q5a10'),
			'q5a11' => $this->input->post('q5a11'),
			'q5a12' => $this->input->post('q5a12'),
			'q5b1' => $this->input->post('q5b1'),
			'q5b2' => $this->input->post('q5b2'),
			'q5b3' => $this->input->post('q5b3'),
			'q5b4' => $this->input->post('q5b4'),
			'q5b5' => $this->input->post('q5b5'),
			'q5b6' => $this->input->post('q5b6'),
			'q5b7' => $this->input->post('q5b7'),
			'q5b8' => $this->input->post('q5b8'),
			'q5b9' => $this->input->post('q5b9'),
			'q5b10' => $this->input->post('q5b10'),
			'q5b11' => $this->input->post('q5b11'),
			'q5b12' => $this->input->post('q5b12'),
			'q5b13' => $this->input->post('q5b13'),
			'q5b14' => $this->input->post('q5b14'),
			'q5c1' => $this->input->post('q5c1'),
			'q5c2' => $this->input->post('q5c2'),
			'q5c3' => $this->input->post('q5c3'),
			'q5c4' => $this->input->post('q5c4'),
			'q5c5' => $this->input->post('q5c5'),
			'q5c6' => $this->input->post('q5c6'),
			'q5c7' => $this->input->post('q5c7')

		);


		return $this->db->insert('sbcp', $data);
	}

	public function sbcp_update()
	{
		$data = array(
			'school_id' => $this->input->post('school_id'),
			'fy' => $this->input->post('fy'),
			'cdate' => $this->input->post('cdate'),
			'district' => $this->input->post('district'),
			'q1a1' => $this->input->post('q1a1'),
			'q1a2' => $this->input->post('q1a2'),
			'q1a3' => $this->input->post('q1a3'),
			'q1a4' => $this->input->post('q1a4'),
			'q1a5' => $this->input->post('q1a5'),
			'q1a6' => $this->input->post('q1a6'),
			'q1a7' => $this->input->post('q1a7'),
			'q1a8' => $this->input->post('q1a8'),
			'q1a9' => $this->input->post('q1a9'),
			'q1a10' => $this->input->post('q1a10'),
			'q1a11' => $this->input->post('q1a11'),
			'q1a12' => $this->input->post('q1a12'),
			'q1a13' => $this->input->post('q1a13'),
			'q1a14' => $this->input->post('q1a14'),
			'q1a15' => $this->input->post('q1a15'),
			'q1a16' => $this->input->post('q1a16'),
			'q1a17' => $this->input->post('q1a17'),
			'q1a18' => $this->input->post('q1a18'),
			'q1a19' => $this->input->post('q1a19'),
			'q1a20' => $this->input->post('q1a20'),
			'q1a21' => $this->input->post('q1a21'),
			'q1b1' => $this->input->post('q1b1'),
			'q1b2' => $this->input->post('q1b2'),
			'q1b3' => $this->input->post('q1b3'),
			'q1b4' => $this->input->post('q1b4'),
			'q1b5' => $this->input->post('q1b5'),
			'q1b6' => $this->input->post('q1b6'),
			'q1b7' => $this->input->post('q1b7'),
			'q1b8' => $this->input->post('q1b8'),
			'q1b9' => $this->input->post('q1b9'),
			'q2a1' => $this->input->post('q2a1'),
			'q2a2' => $this->input->post('q2a2'),
			'q2a3' => $this->input->post('q2a3'),
			'q2a4' => $this->input->post('q2a4'),
			'q2a5' => $this->input->post('q2a5'),
			'q2a6' => $this->input->post('q2a6'),
			'q2a7' => $this->input->post('q2a7'),
			'q2a8' => $this->input->post('q2a8'),
			'q2a9' => $this->input->post('q2a9'),
			'q2a10' => $this->input->post('q2a10'),
			'q2a11' => $this->input->post('q2a11'),
			'q2a12' => $this->input->post('q2a12'),
			'q2a13' => $this->input->post('q2a13'),
			'q2a14' => $this->input->post('q2a14'),
			'q3a1' => $this->input->post('q3a1'),
			'q3a2' => $this->input->post('q3a2'),
			'q3a3' => $this->input->post('q3a3'),
			'q3a4' => $this->input->post('q3a4'),
			'q3a5' => $this->input->post('q3a5'),
			'q3a6' => $this->input->post('q3a6'),
			'q3a7' => $this->input->post('q3a7'),
			'q3a8' => $this->input->post('q3a8'),
			'q3a9' => $this->input->post('q3a9'),
			'q3a10' => $this->input->post('q3a10'),
			'q3b1' => $this->input->post('q3b1'),
			'q3b2' => $this->input->post('q3b2'),
			'q3b3' => $this->input->post('q3b3'),
			'q3b4' => $this->input->post('q3b4'),
			'q3b5' => $this->input->post('q3b5'),
			'q3b6' => $this->input->post('q3b6'),
			'q3b7' => $this->input->post('q3b7'),
			'q3b8' => $this->input->post('q3b8'),
			'q3b9' => $this->input->post('q3b9'),
			'q3b10' => $this->input->post('q3b10'),
			'q3b11' => $this->input->post('q3b11'),
			'q3b12' => $this->input->post('q3b12'),
			'q3b13' => $this->input->post('q3b13'),
			'q3b14' => $this->input->post('q3b14'),
			'q4a1' => $this->input->post('q4a1'),
			'q4a2' => $this->input->post('q4a2'),
			'q4a3' => $this->input->post('q4a3'),
			'q4a4' => $this->input->post('q4a4'),
			'q4a5' => $this->input->post('q4a5'),
			'q4a6' => $this->input->post('q4a6'),
			'q4a7' => $this->input->post('q4a7'),
			'q4a8' => $this->input->post('q4a8'),
			'q4a9' => $this->input->post('q4a9'),
			'q4a10' => $this->input->post('q4a10'),
			'q4b1' => $this->input->post('q4b1'),
			'q4b2' => $this->input->post('q4b2'),
			'q4b3' => $this->input->post('q4b3'),
			'q4b4' => $this->input->post('q4b4'),
			'q4b5' => $this->input->post('q4b5'),
			'q4b6' => $this->input->post('q4b6'),
			'q4b7' => $this->input->post('q4b7'),
			'q4c1' => $this->input->post('q4c1'),
			'q4c2' => $this->input->post('q4c2'),
			'q4c3' => $this->input->post('q4c3'),
			'q4c4' => $this->input->post('q4c4'),
			'q4c5' => $this->input->post('q4c5'),
			'q4c6' => $this->input->post('q4c6'),
			'q4c7' => $this->input->post('q4c7'),
			'q4c8' => $this->input->post('q4c8'),
			'q4c9' => $this->input->post('q4c9'),
			'q4c10' => $this->input->post('q4c10'),
			'q4c11' => $this->input->post('q4c11'),
			'q4c12' => $this->input->post('q4c12'),
			'q4c13' => $this->input->post('q4c13'),
			'q4c14' => $this->input->post('q4c14'),
			'q4c15' => $this->input->post('q4c15'),
			'q4d1' => $this->input->post('q4d1'),
			'q4d2' => $this->input->post('q4d2'),
			'q4d3' => $this->input->post('q4d3'),
			'q4d4' => $this->input->post('q4d4'),
			'q4d5' => $this->input->post('q4d5'),
			'q4d6' => $this->input->post('q4d6'),
			'q4d7' => $this->input->post('q4d7'),
			'q5a1' => $this->input->post('q5a1'),
			'q5a2' => $this->input->post('q5a2'),
			'q5a3' => $this->input->post('q5a3'),
			'q5a4' => $this->input->post('q5a4'),
			'q5a5' => $this->input->post('q5a5'),
			'q5a6' => $this->input->post('q5a6'),
			'q5a7' => $this->input->post('q5a7'),
			'q5a8' => $this->input->post('q5a8'),
			'q5a9' => $this->input->post('q5a9'),
			'q5a10' => $this->input->post('q5a10'),
			'q5a11' => $this->input->post('q5a11'),
			'q5a12' => $this->input->post('q5a12'),
			'q5b1' => $this->input->post('q5b1'),
			'q5b2' => $this->input->post('q5b2'),
			'q5b3' => $this->input->post('q5b3'),
			'q5b4' => $this->input->post('q5b4'),
			'q5b5' => $this->input->post('q5b5'),
			'q5b6' => $this->input->post('q5b6'),
			'q5b7' => $this->input->post('q5b7'),
			'q5b8' => $this->input->post('q5b8'),
			'q5b9' => $this->input->post('q5b9'),
			'q5b10' => $this->input->post('q5b10'),
			'q5b11' => $this->input->post('q5b11'),
			'q5b12' => $this->input->post('q5b12'),
			'q5b13' => $this->input->post('q5b13'),
			'q5b14' => $this->input->post('q5b14'),
			'q5c1' => $this->input->post('q5c1'),
			'q5c2' => $this->input->post('q5c2'),
			'q5c3' => $this->input->post('q5c3'),
			'q5c4' => $this->input->post('q5c4'),
			'q5c5' => $this->input->post('q5c5'),
			'q5c6' => $this->input->post('q5c6'),
			'q5c7' => $this->input->post('q5c7')

		);


		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sbcp', $data);
	}

	// public function sbcp_insert() {
	//     // Define a list of all field keys
	//     $fields = [
	//         'school_id', 'fy', 'cdate', 'district',
	//         // q1a fields
	//         array_map(fn($i) => "q1a$i", range(1, 21)),
	//         // q1b fields
	//         array_map(fn($i) => "q1b$i", range(1, 9)),
	//         // q2a fields
	//         array_map(fn($i) => "q2a$i", range(1, 14)),
	//         // q3a fields
	//         array_map(fn($i) => "q3a$i", range(1, 10)),
	//         // q3b fields
	//         array_map(fn($i) => "q3b$i", range(1, 14)),
	//         // q4a fields
	//         array_map(fn($i) => "q4a$i", range(1, 10)),
	//         // q4b fields
	//         array_map(fn($i) => "q4b$i", range(1, 7)),
	//         // q4c fields
	//         array_map(fn($i) => "q4c$i", range(1, 15)),
	//         // q4d fields
	//         array_map(fn($i) => "q4d$i", range(1, 7)),
	//         // q5a fields
	//         array_map(fn($i) => "q5a$i", range(1, 12)),
	//         // q5b fields
	//         array_map(fn($i) => "q5b$i", range(1, 14)),
	//         // q5c fields
	//         array_map(fn($i) => "q5c$i", range(1, 7))
	//     ];

	//     // Populate the data array by fetching input dynamically
	//     $data = [];
	//     foreach ($fields as $field) {
	//         $data[$field] = $this->input->post($field);
	//     }

	//     // Insert the data into the database
	//     return $this->db->insert('sbcp', $data);
	// }

	// public function sbcp_update() {
	//     // Define a list of all field keys
	//     $fields = [
	//         'school_id', 'fy', 'cdate', 'district',
	//         // q1a fields
	//         array_map(fn($i) => "q1a$i", range(1, 21)),
	//         // q1b fields
	//         array_map(fn($i) => "q1b$i", range(1, 9)),
	//         // q2a fields
	//         array_map(fn($i) => "q2a$i", range(1, 14)),
	//         // q3a fields
	//         array_map(fn($i) => "q3a$i", range(1, 10)),
	//         // q3b fields
	//         array_map(fn($i) => "q3b$i", range(1, 14)),
	//         // q4a fields
	//         array_map(fn($i) => "q4a$i", range(1, 10)),
	//         // q4b fields
	//         array_map(fn($i) => "q4b$i", range(1, 7)),
	//         // q4c fields
	//         array_map(fn($i) => "q4c$i", range(1, 15)),
	//         // q4d fields
	//         array_map(fn($i) => "q4d$i", range(1, 7)),
	//         // q5a fields
	//         array_map(fn($i) => "q5a$i", range(1, 12)),
	//         // q5b fields
	//         array_map(fn($i) => "q5b$i", range(1, 14)),
	//         // q5c fields
	//         array_map(fn($i) => "q5c$i", range(1, 7))
	//     ];

	//     // Populate the data array by fetching input dynamically
	//     $data = [];
	//     foreach ($fields as $field) {
	//         $data[$field] = $this->input->post($field);
	//     }

	//     // Insert the data into the database
	// 	$this->db->where('id', $this->input->post('id'));
	// 	return $this->db->update('sbcp', $data);
	// }

	public function sbm_cecklist_lock_unloc($stat)
	{
		$data = array(
			'stat' => $stat
		);

		$this->db->where('id', $this->uri->segment(3));
		return $this->db->update('sbm', $data);
	}

	public function sbm_ta_lock_unloc($stat)
	{
		$data = array(
			'stat' => $stat
		);

		$this->db->where('id', $this->uri->segment(3));
		return $this->db->update('sbm_ta', $data);
	}

	public function school_id_update()
	{
		$data = array(
			'schoolID' => $this->input->post('school_id')
		);

		$this->db->where('IDNumber', $this->input->post('IDNumber'));
		return $this->db->update('hris_staff', $data);
	}

	public function update_employee_station()
	{
		$data = array(
			'schoolID' => ""
		);

		$this->db->where('IDNumber', $this->uri->segment(3));
		return $this->db->update('hris_staff', $data);
	}

	public function insert_smea()
	{
		$data = array(
			'aip_id' => $this->input->post('aip_id'),
			'q1' => $this->input->post('q1'),
			'q2' => $this->input->post('q2'),
			'q3' => $this->input->post('q3'),
			'q4' => $this->input->post('q4'),
			'total' => $this->input->post('total'),
			'type' => $this->input->post('type')
		);

		return $this->db->insert('sgod_smea', $data);
	}

	

	public function update_smea($id)
	{
		$remarks = "remarks_q".$this->input->post('q');
		$data = array(
			'smea_q1' => $this->input->post('q1'),
			'smea_q2' => $this->input->post('q2'),
			'smea_q3' => $this->input->post('q3'),
			'smea_q4' => $this->input->post('q4'),
			'smea_total' => $this->input->post('total'),
			$remarks => $this->input->post($remarks),
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_sop', $data);
	}

	public function update_smea_ad($id)
	{
		$remarks = "remarks_q".$this->input->post('q');
		$data = array(
			'smea_q1' => $this->input->post('q1'),
			'smea_q2' => $this->input->post('q2'),
			'smea_q3' => $this->input->post('q3'),
			'smea_q4' => $this->input->post('q4'),
			'smea_total' => $this->input->post('total'),
			$remarks => $this->input->post($remarks),
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_sop_adjustment', $data);
	}

	// Submit — or re-submit, after the division office returned the report FOR
	// COMPLIANCE. A school has one row per budget code, so a re-submission
	// updates that row (and clears the SDO decision) instead of inserting a
	// second one.
	public function smea_submit()
	{
		$this->smea_ensure_schema();

		$school_id = $this->uri->segment(3);
		$b_code    = $this->uri->segment(4);
		$fy        = $this->uri->segment(5);

		$data = array(
			'date_submit' => date("Y-m-d H:i:s"),
			'fy' => $fy,
			'remarks' => "Submitted",
			'status' => self::SMEA_SUBMITTED,
			'school_id' => $school_id,
			'b_code' => $b_code,
		);

		$existing = $this->smea_status_row($school_id, $fy, $b_code);
		if ($existing) {
			$data['sdo_remarks']    = null;
			$data['validated_by']   = null;
			$data['date_validated'] = null;

			$this->db->where('id', $existing->id);
			return $this->db->update('sgod_smea', $data);
		}

		return $this->db->insert('sgod_smea', $data);
	}

	public function innovation_insert()
	{
		$data = array(
			'fy' => $this->input->post('fy'),
			'bedp' => $this->input->post('bedp'),
			'innovation' => $this->input->post('innovation'),
			'recipient' => $this->input->post('recipient'),
			'impact' => $this->input->post('impact'),
			'school_id' => $this->session->username
		);

		return $this->db->insert('sgod_innovations', $data);
	}

	public function innovation_update()
	{
		$data = array(
			'fy' => $this->input->post('fy'),
			'bedp' => $this->input->post('bedp'),
			'innovation' => $this->input->post('innovation'),
			'recipient' => $this->input->post('recipient'),
			'impact' => $this->input->post('impact'),
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_innovations', $data);
	}

	public function quick_wins_insert()
	{
		$data = array(
			'fy' => $this->input->post('fy'),
			'qw_type' => $this->input->post('qw_type'), 
			'description' => $this->input->post('description'),
			'impact' => $this->input->post('impact'),
			'school_id' => $this->session->username
		);

		return $this->db->insert('sgod_quick_wins', $data);
	}

	public function quick_wins_update()
	{
		$data = array(
			'fy' => $this->input->post('fy'),
			'qw_type' => $this->input->post('qw_type'), 
			'description' => $this->input->post('description'),
			'impact' => $this->input->post('impact'),
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_quick_wins', $data);
	}

	public function policy_insert()
	{
		$data = array(
			'fy' => $this->input->post('fy'),
			//'ppas' => $this->input->post('ppas'), 
			'oi' => $this->input->post('oi'), 
			'pi' => $this->input->post('pi'),
			'gi' => $this->input->post('gi'), 
			'at' => $this->input->post('at'), 
			'issues' => $this->input->post('issues'),
			'school_id' => $this->session->username
		);

		return $this->db->insert('sgod_policy', $data);
	}

	public function policy_update()
	{
		$data = array(
			'fy' => $this->input->post('fy'),
			//'ppas' => $this->input->post('ppas'), 
			'oi' => $this->input->post('oi'), 
			'pi' => $this->input->post('pi'),
			'gi' => $this->input->post('gi'), 
			'at' => $this->input->post('at'), 
			'issues' => $this->input->post('issues')
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_policy', $data);
	}

	public function smea_adjustment_insert()
	{
		$data = array(
			'sip' => $this->input->post('sip'), 
			'pi' => $this->input->post('pi'), 
			'pillar' => $this->input->post('pillar'), 
			'fy' => $_SESSION['fy'],
			'b_code' => $_SESSION['aip'],
			'school_id' => $this->session->username
		);

		return $this->db->insert('sgod_smea_adjustment', $data);
	}

	public function smea_adjustment_update()
	{
		$data = array(
			'sip' => $this->input->post('sip'), 
			'pi' => $this->input->post('pi'), 
			'pillar' => $this->input->post('pillar')
		);

		
		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_smea_adjustment', $data);
	}

	public function smea_adjust_insert()
	{
		$data = array(
			'aip_id' => $this->input->post('aip_id'), 
			'q1' => $this->input->post('q1'), 
			'q2' => $this->input->post('q2'), 
			'q3' => $this->input->post('q3'), 
			'q4' => $this->input->post('q4'), 
			'total' => $this->input->post('total'), 
			'type' => $this->input->post('type')
		);

		return $this->db->insert('sgod_sop_adjustment', $data);
	}

	public function smea_adjust_update()
	{
		$data = array(
			'q1' => $this->input->post('q1'), 
			'q2' => $this->input->post('q2'), 
			'q3' => $this->input->post('q3'), 
			'q4' => $this->input->post('q4'), 
			'total' => $this->input->post('total'), 
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('sgod_sop_adjustment', $data);
	}

	public function sbfp_update()
	{
		$data = array(
			'height' => $this->input->post('height'), 
			'weight' => $this->input->post('weight'), 
			'age' => $this->input->post('age'), 
			'nut_stat' => $this->input->post('nut_stat'),
			'EnrolledDate' => $this->input->post('EnrolledDate')
		);

		$this->db->where('semstudentid', $this->input->post('id'));
		return $this->db->update('semesterstude', $data);
	}

	// Approved plans for a fiscal year, joined in one pass so the view does not
	// re-query schools/allocation/app-percentage for every row. Pass $status = null
	// for every submitted plan regardless of the stage it has reached.
	// $exclude_statuses is an array of status values to exclude from results.
	public function aip_approved_list($fy, $status = 1, $exclude_statuses = null)
	{
		// The join keys do not share a collation: sgod_aip_submit is utf8mb4_general_ci while
		// schools/sgod_school_allocation are utf8mb4_unicode_ci, so each string comparison needs an
		// explicit COLLATE or MySQL raises error 1267. sgod_app_percentage.b_code is an INT (not a
		// string at all) and its fy is latin1, so that join casts b_code and matches fy to the bound
		// literal instead of to s.fy.
		$fy = $this->db->escape($fy);

		$this->db->select('s.id, s.fy, s.b_code, s.school_id, s.date, s.status, s.remarks,
			sc.schoolName, sc.district, sc.course,
			alloc.alloc_group, alloc.alloc_amount,
			app.id as app_id', false);
		$this->db->from('sgod_aip_submit s');
		$this->db->join(
			'schools sc',
			'sc.schoolID = s.school_id COLLATE utf8mb4_unicode_ci',
			'left',
			false
		);
		$this->db->join(
			'sgod_school_allocation alloc',
			'alloc.schoolID = s.school_id COLLATE utf8mb4_unicode_ci
				AND alloc.alloc_batch = s.b_code COLLATE utf8mb4_unicode_ci',
			'left',
			false
		);
		$this->db->join(
			'sgod_app_percentage app',
			'app.b_code = CAST(s.b_code AS UNSIGNED) AND app.fy = ' . $fy,
			'left',
			false
		);
		$this->db->where('s.fy', $fy, false);
		if ($status !== null) {
			$this->db->where('s.status', $status);
		}
		if ($exclude_statuses !== null && is_array($exclude_statuses) && !empty($exclude_statuses)) {
			$this->db->where_not_in('s.status', $exclude_statuses);
		}
		$this->db->group_by('s.id');
		$this->db->order_by('sc.schoolName', 'ASC');

		return $this->db->get()->result();
	}

	// Unlock ("open AIP") requests for a fiscal year, joined in one pass so the
	// Requested page does not re-query schools/allocation/submission per row.
	// $stat = 0 is the open queue, 1 the already-opened ones, null for both.
	//
	// sgod_aip_request.school_id/b_code are INT while schools.schoolID and
	// sgod_school_allocation.schoolID/alloc_batch are varchar, so the int side is
	// cast to CHAR: a CAST result is coercible to the column's collation, whereas
	// comparing the columns directly forces a numeric conversion of every school
	// row. No GROUP BY is needed - sgod_aip_submit joins on its primary key and
	// (schoolID, alloc_batch) is unique in sgod_school_allocation. sgod_app_percentage
	// is NOT unique per (b_code, fy), so it is read as a scalar subquery instead of a
	// join, which would otherwise multiply the request rows.
	public function aip_request_list($fy, $stat = 0)
	{
		$fy_escaped = $this->db->escape($fy);

		$this->db->select('r.id, r.fy, r.b_code, r.school_id, r.tdate, r.ttime, r.remarks, r.stat, r.s_id,
			sc.schoolName, sc.district, sc.course,
			alloc.alloc_group, alloc.alloc_amount, alloc.alloc_type,
			sub.id AS submit_id, sub.status AS submit_status, sub.remarks AS submit_remarks, sub.date AS submit_date,
			(SELECT MIN(app.id) FROM sgod_app_percentage app
				WHERE app.b_code = r.b_code AND app.fy = ' . $fy_escaped . ') AS app_id', false);
		$this->db->from('sgod_aip_request r');
		$this->db->join(
			'schools sc',
			'sc.schoolID = CAST(r.school_id AS CHAR)',
			'left',
			false
		);
		$this->db->join(
			'sgod_school_allocation alloc',
			'alloc.schoolID = CAST(r.school_id AS CHAR)
				AND alloc.alloc_batch = CAST(r.b_code AS CHAR)',
			'left',
			false
		);
		$this->db->join('sgod_aip_submit sub', 'sub.id = r.s_id', 'left');
		$this->db->where('r.fy', $fy);
		if ($stat !== null) {
			$this->db->where('r.stat', $stat);
		}
		$this->db->order_by('r.id', 'DESC');

		return $this->db->get()->result();
	}

	// Open vs. already-opened unlock request totals for a fiscal year, for the
	// "Requested" card the review / funds / SGOD Chief dashboards share.
	public function aip_request_counts($fy)
	{
		$counts = array('open' => 0, 'opened' => 0, 'total' => 0);

		$this->db->select('stat, COUNT(*) AS total', false);
		$this->db->from('sgod_aip_request');
		$this->db->where('fy', $fy);
		$this->db->group_by('stat');

		foreach ($this->db->get()->result() as $row) {
			$total = (int) $row->total;
			$counts['total'] += $total;

			if ((int) $row->stat === 0) {
				$counts['open'] += $total;
			} else {
				$counts['opened'] += $total;
			}
		}

		return $counts;
	}

	// Per-stage plan counts for a fiscal year in a single grouped query, for the
	// Plan Supervisor dashboard. "submitted" is every plan the schools have sent
	// in for the year whatever stage it sits at now; the other three are the
	// sgod_aip_submit.status values the AIP workflow moves a plan through
	// (3 = AIP Reviewed, 4 = Funds Available, 1 = Approved).
	public function aip_stage_counts($fy)
	{
		$counts = array('submitted' => 0, 'reviewed' => 0, 'funds' => 0, 'approved' => 0);

		$this->db->select('status, COUNT(*) AS total', false);
		$this->db->from('sgod_aip_submit');
		$this->db->where('fy', $fy);
		$this->db->group_by('status');

		foreach ($this->db->get()->result() as $row) {
			$total = (int) $row->total;

			switch ((int) $row->status) {
				case 3:
					$counts['reviewed'] = $total;
					break;
				case 4:
					$counts['funds'] = $total;
					break;
				case 1:
					$counts['approved'] = $total;
					break;
				default:
					$counts['submitted'] += $total;
					break;
			}
		}

		return $counts;
	}

	// SMEA submissions rolled up per district: how many schools in the district have at least one
	// sgod_smea row for the fiscal year, and how many still have none. Done as a single grouped
	// query so the district landing page does not fire one count per school.
	//
	// schools.schoolID is utf8mb4_unicode_ci while sgod_smea.school_id is utf8mb4_0900_ai_ci, so the
	// join key needs an explicit COLLATE or MySQL raises error 1267. Comparing a column to a bound
	// literal (sm.fy) is safe because literals are coercible to the column's collation.
	public function smea_district_summary($fy)
	{
		$fy = $this->db->escape($fy);

		$this->db->select("sc.district,
			COUNT(DISTINCT sc.schoolID) AS total_schools,
			COUNT(DISTINCT sm.school_id) AS submitted,
			COUNT(DISTINCT sc.schoolID) - COUNT(DISTINCT sm.school_id) AS not_submitted,
			MAX(sm.date_submit) AS last_submit", false);
		$this->db->from('schools sc');
		$this->db->join(
			'sgod_smea sm',
			'sm.school_id COLLATE utf8mb4_unicode_ci = sc.schoolID AND sm.fy = ' . $fy,
			'left',
			false
		);
		$this->db->where('sc.district !=', '');
		$this->db->group_by('sc.district');
		$this->db->order_by('sc.district', 'ASC');

		return $this->db->get()->result();
	}

	// Schools of one district for the SMEA drill-down. $filter is 'submitted' (schools with at least
	// one submission for the fiscal year), 'pending' (schools with none) or 'all'. A school can submit
	// once per budget code, so the submissions are aggregated per school.
	public function smea_schools_by_district($district, $fy, $filter = 'submitted')
	{
		$fy = $this->db->escape($fy);

		$this->db->select("sc.schoolID, sc.schoolName, sc.district, sc.course, sc.schoolType,
			COUNT(sm.id) AS submissions,
			MAX(sm.date_submit) AS last_submit,
			GROUP_CONCAT(DISTINCT sm.b_code ORDER BY sm.b_code) AS b_codes", false);
		$this->db->from('schools sc');
		$this->db->join(
			'sgod_smea sm',
			'sm.school_id COLLATE utf8mb4_unicode_ci = sc.schoolID AND sm.fy = ' . $fy,
			'left',
			false
		);
		$this->db->where('sc.district', $district);
		$this->db->group_by('sc.schoolID');

		if ($filter === 'submitted') {
			$this->db->having('submissions >', 0);
		} elseif ($filter === 'pending') {
			$this->db->having('submissions', 0);
		}

		$this->db->order_by('sc.schoolName', 'ASC');

		return $this->db->get()->result();
	}

	// ---------------------------------------------------------------------
	// SMEA validation workflow
	//
	// One sgod_smea row per school + budget code. Its `status` drives both what
	// the school may still touch and what the division office sees:
	//
	//   Submitted      school submitted, waiting for the SDO - school is locked
	//   SDO Validated  accepted by the division office        - school is locked
	//   For Compliance returned with a remark                 - school may edit again
	// ---------------------------------------------------------------------

	const SMEA_SUBMITTED  = 'Submitted';
	const SMEA_VALIDATED  = 'SDO Validated';
	const SMEA_COMPLIANCE = 'For Compliance';

	// The workflow needs four columns on sgod_smea that older installs do not have,
	// and a partial dump import can take them away again. Rather than assume them,
	// repair the schema here — the same field_exists()-guarded pattern the HRIS RQA
	// tables use in Pages.php.
	//
	// Every SMEA read/write below calls this first. It runs at most once per request
	// (static guard) and, once the columns are in place, costs one cached SHOW COLUMNS
	// lookup — CI caches list_fields() per table for the life of the request.
	public function smea_ensure_schema()
	{
		static $checked = false;
		if ($checked) {
			return;
		}
		$checked = true;

		// ADD COLUMN with a NOT NULL default backfills the existing rows for us, so
		// every submission already on file comes back as plain "Submitted".
		$columns = array(
			'status'         => "ADD COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'Submitted' AFTER `remarks`",
			'sdo_remarks'    => "ADD COLUMN `sdo_remarks` TEXT NULL AFTER `status`",
			'validated_by'   => "ADD COLUMN `validated_by` VARCHAR(150) NULL AFTER `sdo_remarks`",
			'date_validated' => "ADD COLUMN `date_validated` DATETIME NULL AFTER `validated_by`",
		);

		$altered = false;
		foreach ($columns as $col => $ddl) {
			if (!$this->db->field_exists($col, 'sgod_smea')) {
				$this->db->query("ALTER TABLE `sgod_smea` $ddl");
				$altered = true;
			}
		}

		if (!$altered) {
			// Steady state: one cached SHOW COLUMNS and nothing else. The indexes below
			// are only checked when a column was missing, because the two always arrive
			// together — a database that has the columns got them either from
			// sgod_smea_validation.sql or from this method, and both create the indexes
			// as well. Checking them on every request would cost three more SHOW INDEX
			// queries to confirm something that cannot be false on any path we control.
			return;
		}

		// list_fields() is cached per table; drop the stale copy so anything asking
		// later in this request sees the columns we just added.
		unset($this->db->data_cache['field_names']['sgod_smea']);

		// Indexes the SMEA pages lean on. sgod_sop in particular is ~57,000 rows with
		// nothing but a primary key, which is what made the report take 6.7s.
		$this->smea_ensure_index('sgod_smea', 'idx_school_fy', '(`school_id`, `fy`)');
		$this->smea_ensure_index('users', 'idx_username', '(`username`)');
		$this->smea_ensure_index('sgod_sop', 'idx_aip_type', '(`aip_id`, `type`)');
	}

	private function smea_ensure_index($table, $name, $cols)
	{
		$exists = $this->db->query(
			"SHOW INDEX FROM `" . $table . "` WHERE Key_name = " . $this->db->escape($name)
		)->row();

		if (empty($exists)) {
			$this->db->query("ALTER TABLE `" . $table . "` ADD INDEX `" . $name . "` " . $cols);
		}
	}

	// The submission row for one school + budget code, or NULL when the school has
	// not submitted that budget code yet.
	public function smea_status_row($school_id, $fy, $bcode)
	{
		$this->smea_ensure_schema();

		$this->db->where('school_id', $school_id);
		$this->db->where('b_code', $bcode);
		if (!empty($fy)) {
			$this->db->where('fy', $fy);
		}
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);

		return $this->db->get('sgod_smea')->row();
	}

	public function smea_row_by_id($id)
	{
		$this->smea_ensure_schema();

		$this->db->where('id', $id);
		return $this->db->get('sgod_smea')->row();
	}

	// Every submission of the given schools for the fiscal year, indexed as
	// [school_id][] so the district drill-down can render per-budget-code actions
	// without one query per row.
	public function smea_submissions_for_schools(array $school_ids, $fy)
	{
		$map = array();
		if (empty($school_ids)) {
			return $map;
		}

		$this->smea_ensure_schema();

		$this->db->where_in('school_id', $school_ids);
		$this->db->where('fy', $fy);
		$this->db->order_by('b_code', 'ASC');

		foreach ($this->db->get('sgod_smea')->result() as $row) {
			$map[(string) $row->school_id][] = $row;
		}

		return $map;
	}

	// Division-office decision on a submission. $status is one of the SMEA_*
	// constants; $remarks is only meaningful for "For Compliance".
	public function smea_set_status($id, $status, $remarks, $by)
	{
		$this->smea_ensure_schema();

		$data = array(
			'status'         => $status,
			'sdo_remarks'    => $remarks,
			'validated_by'   => $by,
			'date_validated' => date('Y-m-d H:i:s'),
		);

		$this->db->where('id', $id);
		return $this->db->update('sgod_smea', $data);
	}
}
