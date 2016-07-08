<?php
/* --------------------------------------------------------------
   test.php 07.07.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
function getTime($before, $after)
{
	return ($after - $before) * 1 . "\n";
}

function _randomAlphabeticLetter($length = 1, $case = null)
{
	$alphabet = [
		'a',
		'b',
		'c',
		'd',
		'e',
		'f',
		'g',
		'h',
		'i',
		'j',
		'k',
		'l',
		'm',
		'n',
		'o',
		'p',
		'q',
		'r',
		's',
		't',
		'u',
		'v',
		'w',
		'x',
		'y',
		'z'
	];
	$result   = '';
	for($i = 0; $i < $length; $i++)
	{
		if($case === 'upper')
		{
			ucfirst($alphabet[mt_rand(0, 25)]);
		}
		elseif($case === 'lower')
		{
			$result .= lcfirst($alphabet[mt_rand(0, 25)]);
		}
		else
		{
			$result .= (mt_rand(0, 1) === 0) ? lcfirst($alphabet[mt_rand(0, 25)]) : ucfirst($alphabet[mt_rand(0, 25)]);
		}
	}

	return $result;
}

function randomAlphabeticLetter($length = 1, $case = null)
{
	;
	$alphabet = 'abcdefghijklmnopqrstuvwxyz';
	$result   = '';
	for($i = 0; $i < $length; $i++)
	{
		if($case === 'upper')
		{
			ucfirst($alphabet[mt_rand(0, 25)]);
		}
		elseif($case === 'lower')
		{
			$result .= lcfirst($alphabet[mt_rand(0, 25)]);
		}
		else
		{
			$result .= (mt_rand(0, 1) === 0) ? lcfirst($alphabet[mt_rand(0, 25)]) : ucfirst($alphabet[mt_rand(0, 25)]);
		}
	}
	
	return $result;
}

$executions = 1000000;

$stringTime = -microtime(true);
for($i = 0; $i < $executions; $i++):
	_randomAlphabeticLetter(10);
endfor;
$stringTime += microtime(true);
echo $stringTime . " seconds\n";

$arrayTime = -microtime(true);
for($i = 0; $i < $executions; $i++):
	randomAlphabeticLetter(10);
endfor;
$arrayTime += microtime(true);
echo $arrayTime . " seconds\n";
