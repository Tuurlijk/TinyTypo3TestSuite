<?php
error_reporting(E_ALL & ~E_NOTICE);
/*****************************************************************************
 *  Copyright notice
 *
 *  ⓒ 2013 Michiel Roos <michiel@maxserv.nl>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is free
 *  software; you can redistribute it and/or modify it under the terms of the
 *  GNU General Public License as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful, but
 *  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *  or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 *  more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ****************************************************************************/

/*****************************************************************************
 *                    Tiny TYPO3 Test Suite v 1.0.0
 *                           by: Michiel Roos
 *                          _______
 *                         /_  __(_)___  __  __
 *                          / / / / __ \/ / / /
 *                         / / / / / / / /_/ /
 *                        /_/ /_/_/ /_/\__, /
 *                                  /____/
 *                   ________  ______  ____ _____
 *                  /_  __/\ \/ / __ \/ __ \__  /
 *                   / /    \  / /_/ / / / //_ <
 *                  / /     / / ____/ /_/ /__/ /
 *                 /_/     /_/_/    \____/____/
 *             ______          __     _____       _ __
 *            /_  __/__  _____/ /_   / ___/__  __(_) /____ 
 *             / / / _ \/ ___/ __/   \__ \/ / / / / __/ _ \
 *            / / /  __(__  ) /_    ___/ / /_/ / / /_/  __/
 *           /_/  \___/____/\__/   /____/\__,_/_/\__/\___/ 
 *
 *           https://github.com/Tuurlijk/TinyTypo3TestSuite
 *
 ****************************************************************************/

/*****************************************************************************
 * Setup
 *
 * - testname: Displayed in the page title and page header
 * - runs    : The default number of runs
 * - skipSets: An array of setNames to skip
 ****************************************************************************/
$testName = '\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode';
$runs = 100;
$skipSets = array('set4', 'set5');

/*****************************************************************************
 * Parameter Sets
 *
 * Define multiple parameter sets here so you can excersise the method well.
 * Each method needs a description. The other parameters must match the
 * parameter names of the method.
 ****************************************************************************/
$parameterSets = array(
	'set1' => array (
		'description' => 'CSV, no removeEmpty, no limit',
		'delim' => ',',
		'string' => 'a, b, c, ,d, e, f',
		'removeEmptyValues' => FALSE,
		'limit' => 0,
	),
	'set2' => array (
		'description' => 'CSV, removeEmpty, no limit',
		'delim' => ',',
		'string' => 'a, b, c, ,d, e, f',
		'removeEmptyValues' => TRUE,
		'limit' => 0,
	),
	'set3' => array (
		'description' => 'CSV, no removeEmpty, limit 3',
		'delim' => ',',
		'string' => 'a, b, c, ,d, e, f',
		'removeEmptyValues' => FALSE,
		'limit' => 3,
	),
	'set4' => array (
		'description' => 'CSV, removeEmpty, limit -3',
		'delim' => ',',
		'string' => 'a, b, c, ,d, e, f',
		'removeEmptyValues' => TRUE,
		'limit' => -3,
	),
	'set5' => array (
		'description' => 'LF, removeEmpty, limit -3',
		'delim' => ',',
		'string' => ' a , b , ' . PHP_EOL . ' ,d ,,  e,f,',
		'removeEmptyValues' => TRUE,
		'limit' => -3,
	),
	'set6' => array (
		'description' => 'Keep 0, removeEmpty, no limit',
		'delim' => ',',
		'string' => 'a , b , c , ,d ,, ,e,f, 0 ,',
		'removeEmptyValues' => TRUE,
		'limit' => 0,
	),
	'set7' => array (
		'description' => 'MarkerArray with | character',
		'delim' => '|',
		'string' => '###|###',
		'removeEmptyValues' => TRUE,
		'limit' => 0,
	),
	'set8' => array (
		'description' => 'String with LF',
		'delim' => chr(10),
		'string' => '######
jaerljwlerj',
		'removeEmptyValues' => TRUE,
		'limit' => 0,
	),

);

/*****************************************************************************
 * Test Methods:
 *
 * Define your test methods here. Name them version1 - version[n].
 * version1 must be the baseline implementation.
 ****************************************************************************/

$descriptions['version1'] = 'Baseline trimExplode';
function version1($delim, $string, $removeEmptyValues = FALSE, $limit = 0) {
	$explodedValues = explode($delim, $string);
	$result = array_map('trim', $explodedValues);
	if ($removeEmptyValues) {
		$temp = array();
		foreach ($result as $value) {
			if ($value !== '') {
				$temp[] = $value;
			}
		}
		$result = $temp;
	}
	if ($limit != 0) {
		if ($limit < 0) {
			$result = array_slice($result, 0, $limit);
		} elseif (count($result) > $limit) {
			$lastElements = array_slice($result, $limit - 1);
			$result = array_slice($result, 0, $limit - 1);
			$result[] = implode($delim, $lastElements);
		}
	}
	return $result;
}

$descriptions['version2'] = 'Optimized baseline';
function version2($delim, $string, $removeEmptyValues = FALSE, $limit = 0) {
	$result = array_map('trim', explode($delim, $string));
	if ($removeEmptyValues) {
		foreach ($result as $key => $value) {
			if ($value === '') {
				unset($result[$key]);
			}
		}
	}
	if ($limit > 0 && count($result) > $limit) {
		$result = array_slice($result, 0, $limit - 1);
		$result[] = implode($delim, array_slice($result, $limit - 1));
	} elseif ($limit < 0) {
		$result = array_slice($result, 0, $limit);
	}
	return $result;
}

$descriptions['version3'] = 'preg_split';
function version3($delim, $string, $removeEmptyValues = FALSE, $limit = 0) {
	$string = trim($string);
	if ($string === '') {
		return array();
	}
	$flags = NULL;
	$pattern = '/\\s*'. preg_quote($delim, '/') . '\\s*/';
	if ($removeEmptyValues) {
		$flags = PREG_SPLIT_NO_EMPTY;
	}
	return preg_split($pattern, $string, $limit, $flags);
}

$descriptions['version4'] = 'preg_split with fallback code for negative limit';
function version4($delim, $string, $removeEmptyValues = FALSE, $limit = 0) {
		if ($limit >= 0) {
			$string = trim($string);
			if ($string === '') {
				return array();
			}
			$flags = NULL;
			$pattern = '/\\s*'. preg_quote($delim, '/') . '\\s*/';
			if ($removeEmptyValues) {
				$flags = PREG_SPLIT_NO_EMPTY;
			}
			return preg_split($pattern, $string, $limit, $flags);
		}
		$result = array_map('trim', explode($delim, $string));
		if ($removeEmptyValues) {
			foreach ($result as $key => $value) {
				if ($value === '') {
					unset($result[$key]);
				}
			}
		}
		return array_slice($result, 0, $limit);
}

/*****************************************************************************
 * Helper Methods:
 *
 * Add any methods that are needed by any of the test methods here.
 ****************************************************************************/


/*****************************************************************************
 * System (look, but don't touch ;-) . . . only touch if you must.
 ****************************************************************************/
$v = '1.0.0';
$reverseExecutionOrder = 0;
if (isset($_GET['source']) && $_GET['source']) {
	show_source(__FILE__);
	exit;
}
if (isset($_GET['runs'])) $runs = preg_replace('/[^0-9]/', '', $_GET['runs']);
if (isset($_GET['reverseExecutionOrder'])) $reverseExecutionOrder = intval($_GET['reverseExecutionOrder']);

// Prepare
$baselineTimes = $functionsToCall = $times = array();
$allFunctions = get_defined_functions();
$functions = array_filter($allFunctions['user'], create_function('$a','return strpos($a, "version") === 0;'));
if ($reverseExecutionOrder) arsort($functions);
foreach ($functions as $function) {
	$xAxis[] = $function;
	$functionsToCall[$function] = new ReflectionFunction($function);
}

// Execute
foreach ($parameterSets as $setName => $parameters) {
	if (in_array($setName, $skipSets)) continue;
	// Description is used later on, so clone the parameters
	$functionParameters = $parameters;
	unset($functionParameters['description']);
	for ($i = 0; $i < $runs; $i++) {
		foreach ($functions as $function) {
			$start = microtime(TRUE);
			$result = $functionsToCall[$function]->invokeArgs($functionParameters);
			$time = microtime(TRUE) - $start;
			if ($function === 'version1') {
				$baselineTimes[$setName] += $time * 1000;
			}
			if (is_array($result)) {
				$resultObjects[$setName][$function] = array_slice($result, 0, 20, TRUE);
			} else {
				$resultObjects[$setName][$function] = $result;
			}
			$times[$setName][$function] += $time * 1000;
		}
	}
}

function findFastestTimes($times) {
	$fastestTimes = array();
	foreach ($times as $setName => $timeData) {
		foreach ($timeData as $functionName => $time) {
			if (isset($fastestTimes[$functionName])) {
				$fastestTimes['overall'][$functionName] += $time;
				$fastestTimes[$setName][$functionName] += $time;
			} else {
				$fastestTimes['overall'][$functionName] = $time;
				$fastestTimes[$setName][$functionName] = $time;
			}
		}
	}
	$fastestTimes = array_filter($fastestTimes, 'asort');
	return $fastestTimes;
}

function findWinner($times) {
	$averagedTimes = array();
	foreach ($times as $timeData) {
		foreach ($timeData as $functionName => $time) {
			$averagedTimes[$functionName] += $time;
		}
	}
	asort($averagedTimes);
	return $averagedTimes;
}

$averagedTimes = findWinner($times);
$fastestTimes = findFastestTimes($times);

/**
 * Format an integer as a time value
 *
 * @param integer $time The value to format
 *
 * @return string
 */
function printSeconds($time) {
	$prefix = '';
	$suffix = 'μs';
	if ($time < 0) {
		$time = abs($time);
		$prefix = '-';
	}
	if ($time === 0) {
		$suffix = '';
	}
	if ($time >= 1000) {
		$time = $time / 1000;
		$suffix = 'ms';
	}
	if ($time >= 1000) {
		$time = $time / 1000;
		$suffix = ' s';
	}
	if ($time >= 60 && $suffix === ' s') {
		$time = $time / 60;
		$suffix = 'min!';
	}
	return $prefix . sprintf("%.2f {$suffix}", $time);
}

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php  echo $testName ?> | Tiny TYPO3 Test Suite v<?php echo $v ?></title>
	<link rel="stylesheet" href="http://wiki.typo3.org/wiki/load.php?debug=false&lang=en&modules=mediawiki.legacy.commonPrint%2Cshared%7Cmediawiki.skinning.interface%7Cmediawiki.ui.button%7Cskins.typo3vector.styles&only=styles&skin=typo3vector&*" />
	<style type="text/css">
		h3 {
			border-bottom: 1px solid #dedede;
		}
		h4 {
			font-family: Share;
			font-weight: normal;
		}
	</style>
	<script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js" type="text/javascript"></script>
	<script src="http://code.highcharts.com/highcharts.js" type="text/javascript"></script>
	<script src="http://code.highcharts.com/modules/exporting.js" type="text/javascript"></script>
</head>
<body>
<div id="content" class="mw-body">
	<h1 id="top"><?php echo $testName ?></h1>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
		<label for="runs">Run the tests how many times?</label>
		<input name="runs" id="runs" value="<?php echo $runs ?>"/>
		<label for="runs"><a href="#help">Reverse execution order?</a></label>
		<input type="checkbox" name="reverseExecutionOrder" id="reverseExecutionOrder" value="1" <?php echo ($reverseExecutionOrder) ? 'checked="checked"' : '' ?>/>
		<input class="submit" type="submit" value="Go!"/>
	</form>
	<div class="timeAveraged" style="float: left;">
		<p>Winner using averaged times over all sets: <strong><?php
			$winner = array_slice($averagedTimes, 0, 1);
			echo key($winner) ?></strong> <?php echo printSeconds(current($winner) / count($times)) ?>.</p>
	<?php
		if (count($parameterSets) > 1) {
			echo '<ul>';
			foreach ($averagedTimes as $function => $time) {
				echo '<li><b>' . $function . '</b>: ',
					' ' . printSeconds($time / count($times)) . '</li>';
			}
			echo '</ul>';
		}
	?>
	</div>
	<div class="timeFastest" style="margin-left: 50%;">
		<p>The fastest function in any set is <strong><?php
			$winner = array_slice($fastestTimes['overall'], 0, 1);
			echo key($winner) ?></strong> <?php echo printSeconds(current($winner)) ?>.</p>
	<?php
		if (count($parameterSets) > 1) {
			echo '<ul>';
			foreach ($fastestTimes as $set => $functions) {
				if ($set !== 'overall') {
					echo '<li><b>' . $set . '</b>: ';
					$setWinner = array_slice($functions, 0, 1);
					echo key($setWinner) . ' ' . printSeconds(current($setWinner)) . '</li>';
				}
			}
			echo '</ul>';
		}
	?>
	</div>
	<div id="resultGraph" style="min-width: 310px; min-height: 400px; margin: 0 auto"></div>
	<h2>Parameter Sets</h2>
	<?php
		foreach ($times as $setName => $functionData) {
			echo '<h3>' , ucfirst($setName) , '</h3>',
				'<p>' , $parameterSets[$setName]['description'] , '</p>',
				'<ul>';
			foreach ($functionData as $function => $time) {
				$identifier = $setName . '-' . $function;
				echo '<li><a style="text-decoration: none" href="#', $identifier, '">',
					ucfirst($function),
					'</a> ',
					': ',
					sprintf('<span style="min-width: 33px; display: inline-block; text-align: right; font-weight: bold;">%1.2d%%</span> ', $time * 100 / $baselineTimes[$setName]),
					sprintf('<span style="min-width: 50px; display: inline-block; text-align: right; margin: 0 10px;">%s</span> ', printSeconds($time)),
					$descriptions[$function],
					'</li>';
			}
			echo '</ul><h4>Parameters</h4><ul>';
			foreach ($parameterSets[$setName] as $key => $value) {
				if ($key !== 'description') {
					if (is_array($value)) {
						echo '<li>' , $key , ':', '</li>'; var_dump($value);
					} else {
						echo '<li>' . $key . ' = ' . (string) $value . '</li>';
					}
				} else {
					$setDescriptions[] = $value;
				}
			}
			echo '</ul>';
		}
	?>
	<script>
		/**
		 * Format an integer as a time value
		 *
		 * @param {String} time The value to format in microseconds.
		 * @param {Number} decimals The amount of decimals
		 *
		 * @return string
		 */
		function printSeconds(time, decimals) {
		   decimals = typeof decimals !== 'undefined' ? decimals : 2;
			var prefix = '',
				suffix = 'μs';
			if (time < 0) {
				time = Math.abs(time);
				prefix = '-';
			}
			if (time == 0) {
				suffix = '';
			}
			if (time >= 1000) {
				time = time / 1000;
				suffix = 'ms';
			}
			if (time >= 1000) {
				time = time / 1000;
				suffix = ' s';
			}
			if (time >= 60 && suffix == ' s') {
				time = time / 60;
				suffix = 'min!';
			}
			return prefix + Highcharts.numberFormat(time, decimals) + ' ' + suffix;
		}

		var baseLineTimes = [<?php echo implode(',', $baselineTimes) ?>];
		var descriptions = ['<?php echo implode("','", array_map('addslashes', $descriptions)) ?>'];
		var setDescriptions = ['<?php echo implode("','", array_map('addslashes', $setDescriptions)) ?>'];
		jQuery(document).ready(function($) {
			$('#resultGraph').highcharts({
				chart: {
					zoomType: 'y'
				},
				title: {
					text: '<?php echo $testName ?>'
				},
				xAxis: {
					categories: ['<?php echo implode("','", $xAxis)  ?>'],
					title: {
						text: null
					}
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Time (milliseconds)',
						align: 'high'
					},
					labels: {
						overflow: 'justify',
						formatter: function() {
							return printSeconds(this.value);
						}
					}
				},
				tooltip: {
					useHTML: true,
					formatter: function() {
						return '<strong><a style="text-decoration: none" href="#' + this.point.series.name + '-' + this.point.category + '">' + this.point.series.name + ', ' + descriptions[this.point.x] + '</a></strong><br/>' +
							setDescriptions[this.series.index] + '<br/>' +
							printSeconds(this.point.y) +
							' (' + Math.ceil(this.point.y * 100 / baseLineTimes[this.point.series.index]) + '%)';
					}
				},
				legend: {
					enabled: true
				},
				credits: {
					enabled: false
				},
				series: [
					<?php
					foreach ($times as $setName => $setTimes) {
						$series[] = "{
							name: '" . $setName . "',
							data: [" . implode(',', $setTimes) . "]
						}";
					}
					echo implode(',', $series);
					?>
				]
			});
			$('#showSourceLink').on('click', function(e) {
				e.preventDefault();
				$.ajax({
					url: '<?php echo $_SERVER['SCRIPT_NAME'] ?>?source=1',
					cache: false
				})
				.done(function(html) {
					$('.loading').hide();
					$('#sourceCode').html(html);
					$('html, body').animate({
						scrollTop: $("#source").offset().top
					}, {
						duration: 2000,
						easing: 'easeOutBounce'
					});
				});
			});
			$('.top').on('click', function(e) {
				e.preventDefault();
				$('html, body').animate({
					scrollTop: $("#top").offset().top
				}, {
					duration: 2000,
					easing: 'easeOutBounce'
				});
			});
		});
	</script>
	<h2>Data results</h2>
	<?php
		foreach ($times as $setName => $functionData) {
			echo '<h3>' . ucfirst($setName) . '</h3>';
			echo '<p>' . $parameterSets[$setName]['description'] . '</p>';
			echo '<ul>';
			foreach ($parameterSets[$setName] as $key => $value) {
				if ($key !== 'description') {
					if (is_array($value)) {
						echo '<li>' , $key , ':', '</li>'; var_dump($value);
					} else {
						echo '<li>' . $key . ' = ' . (string) $value . '</li>';
					}
				}
			}
			echo '</ul>';
			foreach ($functionData as $function => $time) {
				echo '<h4 id="', $setName . '-' . $function, '">', ucfirst($function), '</h4>',
					'<p>', $descriptions[$function], '</p>';
				var_dump($resultObjects[$setName][$function]);
			}
		}
	?>
	<div id="p-personal" role="navigation" class="">
		<ul>
	<?php
	foreach ($resultObjects as $setName => $functionData) {
		foreach ($functionData as $function => $data) {
			echo '<li><a href="#' . $setName . '-' . $function . '">' . ucfirst($setName) . ' - ' . ucfirst($function)  . '</a></h3>';
		}
	}
	?>
			<li><a href="#about">About - v<?php echo $v ?></a></li>
			<li><a href="#help">Help</a></li>
			<li><a href="#source">Source Code</a></li>
		</ul>
	</div>
	<div id="help">
	<div id="about">
		<h2>About</h2>
		<p>Tiny TYPO3 Test Suite v<?php echo $v ?> is a script that helps you test different method implementations. Get the latest version from github:
			<a href="https://github.com/Tuurlijk/TinyTypo3TestSuite">https://github.com/Tuurlijk/TinyTypo3TestSuite</a></p>
	</div>
		<h2>Help</h2>
		<h3>Execution Order</h3>
		<p>In some cases, the second function (non baseline) always runs faster than the baseline. Even when switching the code around. This toggle enables you to reverse the running order to check for this behaviour. Your winning function should still win whatever the execution order. If that is not the case, then this test has failed to determine what code runs faster.</p>
	</div>
	<div id="source">
		<h2>Source Code</h2>
		<pre id="sourceCode"><a href="#source" id="showSourceLink">Show the sourcecode of this file.</a></pre>
	</div>
	<a href="#top" class="top" style="position: fixed; bottom: 10px; left: 25px; text-decoration: none;">^ top</a>
	<a href="https://github.com/Tuurlijk/TinyTypo3TestSuite"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png" alt="Fork me on GitHub"></a>
	<div id="logo" style="position: absolute; top: 60px; left: 60px;"><img alt="" src="data:image/png;base64,
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" /></div>
</div>
</body>
</html>
