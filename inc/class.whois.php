<?php
function whois_server($dominio){

 $whoisservers = array(
 array("biz","whois.neulevel.biz"),
 array("com","whois.verisign-grs.com"),
 array("info","whois.afilias.info"),
 array("org","whois.publicinterestregistry.net"),
 array("net","whois.verisign-grs.com"),
 array("tv","whois.tv"),
 array("co.uk","whois.nic.uk"),
 array("edu","whois.educause.net"),
 array("name","whois.nic.name"),
 array("adm.br","whois.nic.br"),
 array("adv.br","whois.nic.br"),
 array("agr.br","whois.nic.br"),
 array("am.br","whois.nic.br"),
 array("arq.br","whois.nic.br"),
 array("art.br","whois.nic.br"),
 array("ato.br","whois.nic.br"),
 array("bio.br","whois.nic.br"),
 array("bmd.br","whois.nic.br"),
 array("br","whois.nic.br"),
 array("cim.br","whois.nic.br"),
 array("cng.br","whois.nic.br"),
 array("cnt.br","whois.nic.br"),
 array("com.br","whois.nic.br"),
 array("ecn.br","whois.nic.br"),
 array("edu.br","whois.nic.br"),
 array("esp.br","whois.nic.br"),
 array("etc.br","whois.nic.br"),
 array("eti.br","whois.nic.br"),
 array("eng.br","whois.nic.br"),
 array("far.br","whois.nic.br"),
 array("fm.br","whois.nic.br"),
 array("fnd.br","whois.nic.br"),
 array("fot.br","whois.nic.br"),
 array("fst.br","whois.nic.br"),
 array("g12.br","whois.nic.br"),
 array("ggf.br","whois.nic.br"),
 array("gov.br","whois.nic.br"),
 array("ind.br","whois.nic.br"),
 array("imb.br","whois.nic.br"),
 array("inf.br","whois.nic.br"),
 array("jor.br","whois.nic.br"),
 array("lel.br","whois.nic.br"),
 array("mat.br","whois.nic.br"),
 array("med.br","whois.nic.br"),
 array("mil.br","whois.nic.br"),
 array("mus.br","whois.nic.br"),
 array("net.br","whois.nic.br"),
 array("nom.br","whois.nic.br"),
 array("not.br","whois.nic.br"),
 array("ntr.br","whois.nic.br"),
 array("odo.br","whois.nic.br"),
 array("oop.br","whois.nic.br"),
 array("ppg.br","whois.nic.br"),
 array("pro.br","whois.nic.br"),
 array("psi.br","whois.nic.br"),
 array("psc.br","whois.nic.br"),
 array("qsl.br","whois.nic.br"),
 array("rec.br","whois.nic.br"),
 array("slg.br","whois.nic.br"),
 array("srv.br","whois.nic.br"),
 array("tmp.br","whois.nic.br"),
 array("trd.br","whois.nic.br"),
 array("tur.br","whois.nic.br"),
 array("tv.br","whois.nic.br"),
 array("vet.br","whois.nic.br"),
 array("zlg.br","whois.nic.br"));
 
 $whoistotal = count($whoisservers);
 for ($x=0;$x<$whoistotal;$x++){
  $artld = $whoisservers[$x][0];
  $tldlen = intval(0 - strlen($artld));
  if (substr($dominio, $tldlen) == $artld) {
  $whosrv = $whoisservers[$x][1];
  }
 }
 return $whosrv;
}

function whois_resultado($ext){

 $whoisresults = array(
 array("biz","Not found"),
 array("com","No match for"),
 array("info","No match for"),
 array("org","NOT FOUND"),
 array("net","No match for"),
 array("tv","No match"),
 array("co.uk","No match"),
 array("edu","No match for"),
 array("name","No match"),
 array("adm.br","No match for"),
 array("adv.br","No match for"),
 array("agr.br","No match for"),
 array("am.br","No match for"),
 array("arq.br","No match for"),
 array("art.br","No match for"),
 array("ato.br","No match for"),
 array("bio.br","No match for"),
 array("bmd.br","No match for"),
 array("br","No match for"),
 array("cim.br","No match for"),
 array("cng.br","No match for"),
 array("cnt.br","No match for"),
 array("com.br","No match for"),
 array("ecn.br","No match for"),
 array("edu.br","No match for"),
 array("esp.br","No match for"),
 array("etc.br","No match for"),
 array("eti.br","No match for"),
 array("eng.br","No match for"),
 array("far.br","No match for"),
 array("fm.br","No match for"),
 array("fnd.br","No match for"),
 array("fot.br","No match for"),
 array("fst.br","No match for"),
 array("g12.br","No match for"),
 array("ggf.br","No match for"),
 array("gov.br","No match for"),
 array("ind.br","No match for"),
 array("imb.br","No match for"),
 array("inf.br","No match for"),
 array("jor.br","No match for"),
 array("lel.br","No match for"),
 array("mat.br","No match for"),
 array("med.br","No match for"),
 array("mil.br","No match for"),
 array("mus.br","No match for"),
 array("net.br","No match for"),
 array("nom.br","No match for"),
 array("not.br","No match for"),
 array("ntr.br","No match for"),
 array("odo.br","No match for"),
 array("oop.br","No match for"),
 array("ppg.br","No match for"),
 array("pro.br","No match for"),
 array("psi.br","No match for"),
 array("psc.br","No match for"),
 array("qsl.br","No match for"),
 array("rec.br","No match for"),
 array("slg.br","No match for"),
 array("srv.br","No match for"),
 array("tmp.br","No match for"),
 array("trd.br","No match for"),
 array("tur.br","No match for"),
 array("tv.br","No match for"),
 array("vet.br","No match for"),
 array("zlg.br","No match for"));
 
 $whoistotalresults = count($whoisresults);
 for ($x=0;$x<$whoistotalresults;$x++){
  $artld = $whoisresults[$x][0];
  $tldlen = intval(0 - strlen($artld));
  if (substr($ext, $tldlen) == $artld) {
  $whosresult = $whoisresults[$x][1];
  }
 }
 return $whosresult;
}

function whois($dom){
	$lusrv = whois_server($dom);
	$ext = whois_resultado($dom);
	if (!$lusrv) return "";

	$fp = fsockopen($lusrv,43);
	fputs($fp, "$dom\r\n");
	$string="";
	while(!feof($fp)){
		$string.= fgets($fp,128);
	}
	fclose($fp);

	$reg = "/Whois Server: (.*?)\n/i";
	preg_match_all($reg, $string, $matches);
	$secondtry = $matches[1][0];

	if ($secondtry){
		$fp = fsockopen($secondtry,43);
		fputs($fp, "$dom\r\n");
		$string="";
		while(!feof($fp)){
			$string.=fgets($fp,128);
		}
		fclose($fp);
	}
	
	if(preg_match("/$ext/i",$string)) {
        $status = "0|";
	} else {
        $status = "1|";
	}
	return $status.$string;
}
?>
