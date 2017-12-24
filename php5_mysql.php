<?php
        $dbserver = "localhost";
        $dbusername="root";
        $dbpassword="111";
        $title = 'Data Dictionary';
        if($_GET['db'] == ''){
            $database = "data";
        }else{
            $database = $_GET['db'];
        }

        $mysql_conn = @mysql_connect("{$dbserver}","{$dbusername}","{$dbpassword}") or die("Mysql connect is error.");
        
        mysql_select_db($database,$mysql_conn);
        $result = mysql_query('show tables',$mysql_conn);
        //mysql_query('SET NAME GBK',$mysql_conn);
         
        while($row = mysql_fetch_array($result)){
            $tables[]['TABLE_NAME'] = $row[0];
        }
        
        foreach($tables as $k=>$v){
            $sql = 'SELECT * FROM ';
            $sql .= 'INFORMATION_SCHEMA.TABLES ';
            $sql .= 'WHERE ';
            $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
            $table_result = mysql_query($sql,$mysql_conn);
            while($t = mysql_fetch_array($table_result)){
                $tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
            }
            $sql = 'SELECT * FROM ';
            $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
            $sql .= 'WHERE ';
            $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
            
            $fields = array();
            $field_result = mysql_query($sql,$mysql_conn);
            while($t = mysql_fetch_array($field_result)){
                $fields[] = $t;
            }
            $tables[$k]['COLUMN'] = $fields;
        }
        mysql_close($mysql_conn);
        
        $html = '';
         
        foreach($tables as $k=>$v){
            $html .='<table border="1" cellspacing="0" cellpadding="0" align="center">';
            $html .='<caption>'.$v['TABLE_NAME'] .' '.$v['TABLE_COMMENT'] .'</caption>';
            $html .='<tbody><tr><th>NName</th><th>Type</th><th>Defaults</th><th>Null</th><th>Extra</th><th>Comments</th></tr>';
            $html .='';
            
            foreach($v['COLUMN'] AS $f){
                $html .='<td class="c1">'.$f['COLUMN_NAME'].'</td>';
                $html .='<td class="c2">'.$f['COLUMN_TYPE'].'</td>';
                $html .='<td class="c3">'.$f['COLUMN_DEFAULT'].'</td>';
                $html .='<td class="c4">'.$f['IS_NULLABLE'].'</td>';
                $html .='<td class="c5">'.($f['EXTRA']=='auto_increment'?'YES':' ').'</td>';
                $html .='<td class="c6">'.$f['COLUMN_COMMENT'].'</td>';
                $html .= '</tr>';
            }
            $html .='</tbody></table></p>';
        }
        echo '<html>
    <meta charset="gbk">
    <title>Data Dictionary</title>
    <style>
        body,td,th {font-size:12px;}  
        table{border-collapse:collapse;border:1px solid #CCC;background:#efefef;}  
        table caption{text-align:left; background-color:#fff; line-height:2em; font-size:14px; font-weight:bold; }  
        table th{text-align:left; font-weight:bold;height:26px; line-height:26px; font-size:12px; border:1px solid #CCC;}  
        table td{height:20px; font-size:12px; border:1px solid #CCC;background-color:#fff;}  
        .c1{ width: 120px;}  
        .c2{ width: 120px;}  
        .c3{ width: 70px;}  
        .c4{ width: 80px;}  
        .c5{ width: 80px;}  
        .c6{ width: 270px;}
    </style>
    <body>';
    echo '<h1 style="text-align:center;">'.$title.'</h1>';
    echo $html;
    echo '</body></html>';
        ?>