<?php
	/*
		Working with XML. Usage:
		$xml=xml2ary(file_get_contents('1.xml'));
		$link=&$xml['ddd']['_c'];
		$link['twomore']=$link['onemore'];
		// ins2ary(); // dot not insert a link, and arrays with links inside!
		echo ary2xml($xml);
	*/

	// XML to Array
	function xml2ary(&$string) {
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $string, $vals, $index);
		xml_parser_free($parser);

		$mnary=array();
		$ary=&$mnary;
		foreach ($vals as $r) {
			$t=$r['tag'];
			if ($r['type']=='open') {
				if (isset($ary[$t])) {
					if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
					$cv=&$ary[$t][count($ary[$t])-1];
				} else $cv=&$ary[$t];
				if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
				$cv['_c']=array();
				$cv['_c']['_p']=&$ary;
				$ary=&$cv['_c'];

			} elseif ($r['type']=='complete') {
				if (isset($ary[$t])) { // same as open
					if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
					$cv=&$ary[$t][count($ary[$t])-1];
				} else $cv=&$ary[$t];
				if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
				$cv['_v']=(isset($r['value']) ? $r['value'] : '');

			} elseif ($r['type']=='close') {
				$ary=&$ary['_p'];
			}
		}

		_del_p($mnary);
		return $mnary;
	}

	// _Internal: Remove recursion in result array
	function _del_p(&$ary) {
		foreach ($ary as $k=>$v) {
			if ($k==='_p') unset($ary[$k]);
			elseif (is_array($ary[$k])) _del_p($ary[$k]);
		}
	}

?>
