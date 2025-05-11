<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$store = './user/'; 

@$housekeeping = 24*60*60;

@$view = $_GET['view'];
@$geo = $_GET['geo'];
@$user = $_GET['user'];
@$delete = $_GET['delete'];

####### Locus Pro Parameters  ###############
@$lat = $_GET['lat'];
@$lon = $_GET['lon'];
@$alt = $_GET['alt'];
@$speed = $_GET['speed'];
@$acc = $_GET['acc'];
@$battery = $_GET['battery'];
@$gsm_signal = $_GET['gsm_signal'];
@$bearing = $_GET['bearing'];
@$message = $_GET['message'];
##############################################

# Current Unix timestamp
$time = time();
$now = $time;

# JSON output
#echo header('application/json');
header('Content-Type: application/json;charset=utf-8');
echo '{';

# user ellenorzes
if ($user!="") {
        $geojson = $store.$user.".geojson";
        $store .= "$user.latlon";

	##### Write to file
        # latlon fajl irasa
        if ( $lat!="" && $lon!="" ) {
		if ($handle = fopen($store, "w")){
			fwrite($handle,
				'"lat":"'.$lat.
				'","lon":"'.$lon.
				'","alt":"'.$alt.
				'","speed":"'.$speed.
				'","bearing":"'.$bearing.
				'","acc":"'.$acc.
				'","time":"'.$time.
				'","battery":"'.$battery.
				'","gsm_signal":"'.$gsm_signal.
				'","message":"'.$message.'"');
			fclose($handle);}
		else{ echo "File error .latlon";}

		# trakc fajl geojson
		if ($handle = fopen($geojson, "a+")){
			fwrite($handle,"[ ".$lon.", ".$lat." ], ");
			fclose($handle);}
		else{ echo "File error .geojson";}
	} #if lat, lon
	else if ($view == '1' ){
		if ($handle = fopen($store, "r")){
			$contents=fread($handle,filesize($store));
			$contents=$contents.',"now":"'.$now.'"';
			echo $contents;
			fclose($handle);}
		else{ echo 'Dateilesefahler view=1';}
	}
	else if ($geo == '1'){
		if ($handle = fopen($geojson, "r")){
			$contents=fread($handle,filesize($geojson));
			//Get rid of last ","
			$contents=substr($contents,0,strrpos($contents,","));
			$contents=' "type": "FeatureCollection",
			"features": [
			{ "type": "Feature", "geometry": 
			{ "type": "MultiLineString", 
			"coordinates": [['.$contents.']]}}]';
			echo $contents;
			fclose($handle);} 
		else{ echo 'Read error geo=1';}

	# Delete .latlon & .geojson by purpose
	}
	else if ($delete == '1'){ unlink($geojson); unlink($store);}
	else{ echo '"error":"bad parameters"';}

} # if user
else { echo 'Private key is missing';}

# Done!
echo '}';
?>
