<?php

$main = new main();
$main->run();
echo("Exit:NoErr");
exit(0);

class main{
	const MAX_ELECTRONS_IN_ORBITAL = 2; 				#Maximale Elektronen in EINEM Orbital

	public $doExit = false;

	public $basicPerodicElementsAlignment = [			#Periodensystem
	1 => ["H",NULL,NULL,NULL,NULL,NULL,NULL,"He"],
	2 => ["Li","Be","B","C","N","O","F","Ne"],
	3 => ["Na","Mg","Al","Si","P","S","Cl","Ar"],
	4 => ["K","Ca","Ga","Ge","As","Se","Br","Kr"],
	5 => ["Rb","Sr","In","Sn","Sb","Te","I","Xe"],
	6 => ["Cs","Ba","TI","Pb","Bi","Po","At","Rn"],
	7 => ["Fr","Ra","Uut","Uuq","Uup","Uuh","Uus","Uuo"]
	];

	public $electronsPerElement = [						#Wie viele Elektronen ein
	1 => [1,NULL,NULL,NULL,NULL,NULL,NULL,2],			#Element hat
	2 => [3,4,5,6,7,8,9,10],
	3 => [11,12,13,14,15,16,17,18],
	4 => [19,20,31,32,33,34,35,36],
	5 => [37,38,49,50,51,52,53,54],
	6 => [55,56,81,82,83,84,85,86],
	7 => [87,88,113,114,115,116,117,118],
	];

	public $shellPeriodAlignment = [ 					#Per Periode welche Schalen vor-
	1 => ["K",NULL,NULL,NULL,NULL,NULL,NULL,],			#handen sind!
	2 => ["K","L",NULL,NULL,NULL,NULL,NULL,],
	3 => ["K","L","M",NULL,NULL,NULL,NULL,],
	4 => ["K","L","M","N",NULL,NULL,NULL,],
	5 => ["K","L","M","N","O",NULL,NULL,],
	6 => ["K","L","M","N","O","P",NULL,],
	7 => ["K","L","M","N","O","P","Q",],
	];

	public $orbitalPerShell = [							#Welche Schalen welche Typen von
	"K" => ["s", NULL, NULL, NULL],						#Orbitalen halten
	"L" => ["s", "p", NULL, NULL],	
	"M" => ["s", "p", "d", NULL],	
	"N" => ["s", "p", "d", "f"],
	"O" => ["s", "p", "d", "f"],
	"P" => ["s", "p", "d", "f"],
	"Q" => ["s", "p", "d", "f"],
	];
	public $orbitalsContainOrbitals = [					#Welche Typen von Orbitalen wie
	"s" => 1,"p" => 3,"d" => 5,"f" => 6					#viele Orbitale halten, welche
	];													#maximal 2 Elektronen halten können!

	public $mainGroupNumbers = [						#Normale Zahlen zu Römischen Zahlen
	1 => "I",2 => "II",3 => "III",4 => "IV",
	5 => "V",6 => "VI",7 => "VII",8 => "VIII"
	];

	public function __construct(){
		echo "Command? (type help to get a list of avaivible commands)".PHP_EOL;
	}

	public function run(){
		while(!$this->doExit){
  			$input = stream_get_line(STDIN, 1024, PHP_EOL);
  			$args = explode(" ",$input);
  			$cmd = $args[0];
  			$args[0] = NULL;
  			switch($cmd){
  				case "help":
  					echo("exit               - Exits this script".PHP_EOL);
  					echo("showInfo <Element> - Shows information about a element".PHP_EOL);
  				break;
  				case "exit":
  					$this->shutdown();
  				break;
  				case "showInfo":
  					$this->showInfo($args[1]);
  				break;
  				default:
  					echo("Unknown command '".$cmd."' (type help to get a list of avaivible commands)".PHP_EOL);
  				break;
  			}
		}
	}

	public function showInfo($element){
		$period = $this->getPeriod($element);
  		if(!$period){
  			echo("Could not find element '".$element."'!".PHP_EOL);
  			echo("Exit:ERR");
  			exit(1);
  		}
  		$mainGroup = $this->getMainGroup($element);
  		if(!$mainGroup){
  			echo("Could not find element '".$element."'!".PHP_EOL);
  			echo("Exit:ERR");
  			exit(1);
  		}
  		$latinMainGroup = $this->convertNumbersToLatinNumbers($mainGroup);
  		if(!$latinMainGroup){
  			echo("Error while converting '".$mainGroup."' to a MainGroupIndex!".PHP_EOL);
  			echo("Exit:ERR");
  			exit(1);
  		}
  		$elementID = $this->getElementID($element);
  		if(!$elementID){
  			echo("Could not find element '".$element."'!".PHP_EOL);
  			echo("Exit:ERR");
  			exit(1);
  		}
  		$electrons = $this->getElectrons($element);
  		if(!$electrons){
  			echo("Unkown ERROR while calculating Electrons of '".$element."'!".PHP_EOL);
  			echo("Exit:ERR");
  			exit(1);
  		}
  		$valenceElectrons = $this->getValenceElectrons($element);
  		if(!$valenceElectrons){
  			echo("Unkown ERROR while calculating ValenceElectrons of '".$element."'!".PHP_EOL);
  			echo("Exit:ERR");
  			exit(1);
  		}
  		echo("[Internal] ElementID of '".$element."': ".$elementID.PHP_EOL);
		echo("Period of '".$element."': ".$period.PHP_EOL);
		echo("Main Group of '".$element."': ".$latinMainGroup.PHP_EOL);
		echo("Electrons of '".$element."': ".$electrons.PHP_EOL);
		echo("ValenceElectrons of '".$element."': ".$valenceElectrons.PHP_EOL);
	}

	public function shutdown(){
		$this->doExit = true;
	}

	public function convertNumbersToLatinNumbers($number){
		if(isset($this->mainGroupNumbers[$number])){
			return $this->mainGroupNumbers[$number];
		}
		return false;
	}

	public function getElementID($element){				#Gibt die internale ElementID
		if($element == NULL || $element == ""){			#von $element zurück
			return false;
		}
		$currentElementID = 0;
		foreach($this->basicPerodicElementsAlignment as $periodElements){
			foreach($periodElements as $currentElement){
				$currentElementID++;
				if($currentElement == $element){
					return $currentElementID;
				}
			}
		}
		return false;
	}

	public function getPeriod($element){				#Gibt die PERIODE von $element
		if($element == NULL || $element == ""){			#zurück
			return false;
		}
		foreach($this->basicPerodicElementsAlignment as $currentPeriod => $periodElements){
			foreach($periodElements as $currentElement){
				if($currentElement == $element){
					return $currentPeriod;
				}
			}
		}
		return false;
	}

	public function getMainGroup($element){
		if($element == NULL || $element == ""){
			return false;
		}
		$currentMainGroup = 0;
		foreach($this->basicPerodicElementsAlignment as $currentPeriod => $periodElements){
			foreach($periodElements as $currentElement){
				$currentMainGroup++;
				if($currentElement == $element){
					return $currentMainGroup;
				}
			}
			$currentMainGroup = 0;
		}
		return false;
	}

	public function getElectrons($element){
		$elementID = $this->getElementID($element);
  		if(!$elementID){
  			echo("Could not find element '".$element."'!".PHP_EOL);
  			echo("Exit:ERR");
  			exit(1);
  		}
  		$currentElementID = 0;
		foreach($this->electronsPerElement as $periodElementsElectrons){
			foreach($periodElementsElectrons as $currentElementElectrons){
				$currentElementID++;
				if($currentElementID == $elementID){
					return $currentElementElectrons;
				}
			}
		}
		return false;
	}

	public function getValenceElectrons($element){
		if($element == NULL || $element == ""){
			return false;
		}
  		$period = $this->getPeriod($element);
  		if(!$period){
  			echo("Could not find element '".$element."'!".PHP_EOL);
			return false;
  		}
  		return $period;
  		return false;
	}
}