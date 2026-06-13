<?php


class SmeaModel extends CI_Model
{

public function smea_by_pillar($con1, $con2, $con3, $con4, $q,$type) {
    $this->db->select('a.*, b.*');
    $this->db->from('sgod_aip as a');
    $this->db->join('sgod_sop as b', 'a.id = b.aip_id', 'left');
    $this->db->where('a.school_id', $con1);
    $this->db->where('a.fy', $con2);
    $this->db->where('a.b_code', $con3);
    $this->db->where('a.pillar', $con4);
    
    $this->db->where('b.type', $type);
    $this->db->where('b.'.$q.' !=', 0);
    $this->db->where('b.'.$q.' !=', '');

    $query = $this->db->get();
    return $query;
}

public function smea_gap($con1, $con2, $con3, $con4, $q,$type) {
    $this->db->select('a.*, b.*');
    $this->db->from('sgod_aip as a');
    $this->db->join('sgod_sop as b', 'a.id = b.aip_id', 'left');
    $this->db->where('a.school_id', $con1);
    $this->db->where('a.fy', $con2);
    $this->db->where('a.b_code', $con3);
    $this->db->where('a.pillar', $con4);
    
    $this->db->where('b.type', $type);
    $this->db->where('b.'.$q.' !=', 0);
    $this->db->where('b.'.$q.' !=', '');
    $this->db->where('b.q1 > b.smea_q1');

    $query = $this->db->get();
    return $query;
}

public function smea_gane($con1, $con2, $con3, $con4, $q,$type) {
    $this->db->select('a.*, b.*');
    $this->db->from('sgod_aip as a');
    $this->db->join('sgod_sop as b', 'a.id = b.aip_id', 'left');
    $this->db->where('a.school_id', $con1);
    $this->db->where('a.fy', $con2);
    $this->db->where('a.b_code', $con3);
    $this->db->where('a.pillar', $con4);
    
    $this->db->where('b.type', $type);
    $this->db->where('b.'.$q.' !=', 0);
    $this->db->where('b.'.$q.' !=', '');
    $this->db->where('b.q1 < b.smea_q1');

    $query = $this->db->get();
    return $query;
}

    
}



?>