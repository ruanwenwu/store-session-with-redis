<?php
/**
 * MySQL ���ݿ����������, �������ݿ����.
 * ʾ�����ײ�ע��.
 * @author: cuitengwei
 */
class DBCUI{
    var $conn;
    var $query_list = array();
    public $query_count = 0;

    public function __construct($c){
        if(!isset($c['port'])){
            $c['port'] = '3306';
        }
        $server = $c['host'] . ':' . $c['port'];
        $this->conn = mysql_connect($server, $c['username'], $c['password'], true) or die('connect db error');
        mysql_select_db($c['dbname'], $this->conn) or die('select db error');
        if($c['charset']){
            mysql_query("set names " . $c['charset'], $this->conn);
        }
    }

    /**
     * ִ�� mysql_query ����������.
     */
    public function query($sql){
        $stime = microtime(true);

        $result = mysql_query($sql, $this->conn);
        $this->query_count ++;
        if($result === false){
            //throw new Exception(mysql_error($this->conn)." in SQL: $sql");
        }

        $etime = microtime(true);
        $time = number_format(($etime - $stime) * 1000, 2);
        $this->query_list[] = $time . ' ' . $sql;
        return $result;
    }

    /**
     * ִ�� SQL ���, ���ؽ���ĵ�һ����¼(��һ������).
     */
    public function get($sql){
        $result = $this->query($sql);
        if($row = mysql_fetch_object($result)){
            return $row;
        }else{
            return null;
        }
    }
	
	/**
	 *	����sqlִ�й���Ľ������������һ������
	 */
	public function fetch_array($sql){
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$res = $row[0];
		return $res;
	}

    /**
     * ���ز�ѯ�����, �� key Ϊ����֯�ɹ�������, ÿһ��Ԫ����һ������.
     * ��� key Ϊ��, �򽫽����֯����ͨ������.
     */
    public function find($sql, $key=null){
        $data = array();
        $result = $this->query($sql);
        while($row = mysql_fetch_object($result)){
            if(!empty($key)){
                $data[$row->{$key}] = (array)$row;
            }else{
                $data[] = (array)$row;
            }
        }
        return $data;
    }

    /**
     * ֻ����һ��
     */
    public function fetch_first($sql){
        return mysql_fetch_array($this->query($sql), MYSQL_ASSOC);
    }

    public function last_insert_id(){
        return mysql_insert_id($this->conn);
    }

    /**
     * ִ��һ�����н���������� count SQL ���, �����ü���.
     */
    public function count($sql){
        $result = $this->query($sql);
        if($row = mysql_fetch_array($result)){
            return (int)$row[0];
        }else{
            return 0;
        }
    }

    /**
     * ��ʼһ������.
     */
    public function begin(){
        mysql_query('begin');
    }

    /**
     * �ύһ������.
     */
    public function commit(){
        mysql_query('commit');
    }

    /**
     * �ع�һ������.
     */
    public function rollback(){
        mysql_query('rollback');
    }

    /**
     * ��ȡָ����ŵļ�¼.
     * @param int $id Ҫ��ȡ�ļ�¼�ı��.
     * @param string $field �ֶ���, Ĭ��Ϊ'id'.
     */
    function load($table, $id, $field='id',$fd='*'){
        $sql = "select $fd from `{$table}` where `{$field}`='{$id}'";
        $row = $this->get($sql);
        return $row;
    }

    /**
     * ����һ����¼, ���ú�, id������.
     * @param object $row
     */
    function add($table, &$row){
        $sqlA = '';
        foreach($row as $k=>$v){
            $sqlA .= "`$k` = '".addslashes($v)."',";
        }

        $sqlA = substr($sqlA, 0, strlen($sqlA)-1);
        $sql  = "insert into `{$table}` set $sqlA";

        $this->query($sql);
        if(is_object($row)){
            unset($row);
            unset($sqlA);
            return $this->last_insert_id();
        }else if(is_array($row)){
            unset($row);
            unset($sqlA);
            return  $this->last_insert_id();
        }
    }

    /**
     * ����$arr[id]��ָ���ļ�¼.
     * @param array $row Ҫ���µļ�¼, ����Ϊid���������ֵָʾ����Ҫ���µļ�¼.
     * @return int Ӱ�������.
     * @param string $field �ֶ���, Ĭ��Ϊ'id'.
     */
    function update($table, &$row, $field='id'){
        $sqlA = '';
        foreach($row as $k=>$v){
            $sqlA .= "`$k` = '".addslashes($v)."',";
        }

        $sqlA = substr($sqlA, 0, strlen($sqlA)-1);
        if(is_object($row)){
            $id = $row->{$field};
        }else if(is_array($row)){
            $id = $row[$field];
        }
        $sql  = "update `{$table}` set $sqlA where `{$field}`='$id'";
        $rowsta = $this->query($sql);
        unset($row);
        unset($sqlA);
        return $rowsta==1?$rowsta:$sql;
    }

    /**
     * ����$arr[id]��ָ���ļ�¼.
     * @param array $row Ҫ���µļ�¼, ����Ϊid���������ֵָʾ����Ҫ���µļ�¼.
     * @return int Ӱ�������.
     * @param string $field �ֶ���, Ĭ��Ϊ'id'.
     */
    function edit($table, &$row, $where){
        if ($where) {
            $sqlA = '';
            foreach($row as $k=>$v){
                $sqlA .= "`$k` = '".addslashes($v)."',";
            }

            $sqlA = substr($sqlA, 0, strlen($sqlA)-1);
            $sql  = "update `{$table}` set $sqlA where {$where}";

            $rowsta = $this->query($sql);
            unset($row);
            unset($sqlA);
            return $rowsta==1?$rowsta:$sql;
        }else{
            return false;
        }

        
    }

    /**
     * ɾ��һ����¼.
     * @param int $id Ҫɾ���ļ�¼���.
     * @return int Ӱ�������.
     * @param string $field �ֶ���, Ĭ��Ϊ'id'.
     */
    function remove($table, $id, $field='id'){
        $sql  = "delete from `{$table}` where `{$field}`='{$id}'";
        return $this->query($sql);
    }

    function escape(&$val){
        if(is_object($val) || is_array($val)){
            $this->escape_row($val);
        }
    }

    function escape_row(&$row){
        if(is_object($row)){
            foreach($row as $k=>$v){
                $row->$k = mysql_real_escape_string($v);
            }
        }else if(is_array($row)){
            foreach($row as $k=>$v){
                $row[$k] = mysql_real_escape_string($v);
            }
        }
    }

    function escape_like_string($str){
        $find = array('%', '_');
        $replace = array('\%', '\_');
        $str = str_replace($find, $replace, $str);
        return $str;
    }
}


/*
// ����
$db->add('table_1', $row);
// ����
$db->update('table_1', $row);
// ɾ��
$db->remove('table_1', 1);
// ��ѯ
$rows = $db->find($sql, 'id')

*/
?>