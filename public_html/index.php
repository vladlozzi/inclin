<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Inclinometering :: ІФНТУНГ</title>
  <style media="print"> .noprint {display: none;} </style>
  <style>
    header {
      position: fixed; height: 120px; margin-top: -120px;
      margin-left: auto; margin-right: auto; width: 100%;
      background-color: RGB(227, 227, 227);
    }
    article { margin-top: 120px; }
    table {
		  width: 50%; /* Ширина таблиці */
		  margin-left: auto; margin-right: auto; /* Таблиця по центру*/
  		border: 2px double black; /* Рамка навколо таблиці */
		  border-collapse: collapse; /* Відображати тільки одинарні лінії */
    }
	  th {
		  text-align: center; /* Вирівнювання по центру */
		  background: #ccc; /* Колір фону клітинок */
		  padding: 2px; /* Поля навколо вмісту клітинок */
		  border: 1px solid black; /* Межі навколо клітинок */
    }
	  td {
      font-size: 100%; font-family: sans-serif;
		  vertical-align: top; /* Вертикальне вирівнювання згори  */
		  text-align: center; /* Вирівнювання по центру */
		  padding: 2px; /* Поля навколо вмісту клітинок */
		  border: 1px solid black; /* Межі навколо клітинок */
    }
   </style>
</head>
<body><?php session_start(); ?>
	<header>
		<h3 style="text-align: center; margin: 0% auto -2% auto; ">
			Івано-Франківський національний технічний університет нафти і газу</h3>
		<h3 style="text-align: center; margin: 2% auto -3% auto; ">
			Кафедра геології та розвідки нафтових і газових родовищ</h3>
		<h1 style="text-align: center; margin: 4% auto 0% auto; ">
			Моделювання профілю свердловини за даними інклінометрії</h1>
	</header>
	<article><?php
		ini_set("error_reporting", E_ALL); ini_set("display_errors", 1); ini_set("display_startup_errors", 1);
		define("From_Index", TRUE); $tdStyle = ' style="border: 1px solid black;"';
		$tdCenterStyle = ' style="border: 1px solid black; text-align: center;"';
		$_POST['txtName'] = isset($_POST['txtName']) ? $_POST['txtName'] : "";
		$_POST['txtField'] = isset($_POST['txtField']) ? $_POST['txtField'] : "";
		$_POST['txtWell'] = isset($_POST['txtWell']) ? $_POST['txtWell'] : "";
		$_POST['nmbAlt'] = isset($_POST['nmbAlt']) ? $_POST['nmbAlt'] : "0";
		$_POST['nmbXProf'] = isset($_POST['nmbXProf']) ? $_POST['nmbXProf'] : "0";
		$_POST['nmbDepth'] = isset($_POST['nmbDepth']) ? $_POST['nmbDepth'] : "1000";
		$_POST['nmbVert'] = isset($_POST['nmbVert']) ? $_POST['nmbVert'] : "100";
		$_POST['nmbStep'] = isset($_POST['nmbStep']) ? $_POST['nmbStep'] : "25"; ?>
		<form id="formDatas" method="post" target="_self">
		<p style="text-align: center; font-size: 120%; margin: 1% auto 1% auto;">
			Виконавець:
			<input style="width: 400px; font-size: 100%;" type="text" name="txtName" required value="<?php echo $_POST['txtName']; ?>"><br>
			Родовище (площа):
			<input style="width: 400px; font-size: 100%;" type="text" name="txtField" required value="<?php echo $_POST['txtField']; ?>"><br>
			Свердловина:
			<input style="width: 100px; font-size: 100%;" type="text" name="txtWell" required value="<?php echo $_POST['txtWell']; ?>"> &nbsp; &nbsp;
			Альтитуда устя, м:
			<input style="width: 80px; font-size: 100%;" type="number" min="-100" max=1500
						name="nmbAlt" required value="<?php echo $_POST['nmbAlt']; ?>"><br>
			Координата устя на профілі, м:
			<input style="width: 80px; font-size: 100%;" type="number" min=0 max=10000
						name="nmbXProf" required value="<?php echo $_POST['nmbXProf']; ?>"> &nbsp; &nbsp;
			Довжина свердловини, м:
			<input style="width: 80px; font-size: 100%;" type="number" min=100 max=10000
						name="nmbDepth" required value="<?php echo $_POST['nmbDepth']; ?>"><br>
			Вертикальна ділянка, м:
			<input style="width: 80px; font-size: 100%;" type="number" min=0 max=1000
						name="nmbVert" required value="<?php echo $_POST['nmbVert']; ?>"> &nbsp; &nbsp;
			Крок інклінометрії, м:
			<input style="width: 80px; font-size: 100%;" type="number" min=10 max=100 step=5
						name="nmbStep" required value="<?php echo $_POST['nmbStep']; ?>" onchange="submit()"><br>
		</p><?php /* Визначення hInit - глибини початку інклінометрії */
		$hInit = $_POST['nmbVert'] + $_POST['nmbStep']; ?>
		<table>
			<thead style="font-size: 120%;">
				<tr><th rowspan=2>№</th><th colspan=3>Початкові дані інклінометрії</th><th colspan=5>Результати обробки</th></tr>
          <tr><th>Довжина<br>L, м</th><th>Зенітний<br>кут Z</th><th>Азимут A</th>
            <th>X, м</th><th>Y, м</th><th>Відхід<br>вибою &delta;, м</th><th>Вертикаль<br>H, м</th><th>&Delta;L, м</th></tr>
        </thead>
        <tbody><?php $X = array(); $Y = array(); $HV = array(); $Dlt = array();
        $X[0] = 0; $Y[0] = 0; $HV[0] = $_POST['nmbVert']; $Dlt[0] = 0;
      $_POST['sbtCalc'] = isset($_POST['sbtCalc']) ? $_POST['sbtCalc'] : "";
      if (!empty($_POST['sbtCalc'])) {
        $i = 1;
        for ($h = $hInit; $h <= $_POST['nmbDepth']; $h += $_POST['nmbStep']) {
          $_POST['nmbZenDeg'.$h] = isset($_POST['nmbZenDeg'.$h]) ? $_POST['nmbZenDeg'.$h] : 0;
          $_POST['nmbZenMin'.$h] = isset($_POST['nmbZenMin'.$h]) ? $_POST['nmbZenMin'.$h] : 0;
          $_POST['nmbAzim'.$h] = isset($_POST['nmbAzim'.$h]) ? $_POST['nmbAzim'.$h] : 0;
          $ZenHInRad = deg2rad($_POST['nmbZenDeg'.$h]+$_POST['nmbZenMin'.$h] / 60);
          $AzimInRad = deg2rad($_POST['nmbAzim'.$h]);
          $X[$i] = $X[$i - 1] + $_POST['nmbStep'] * sin($ZenHInRad) * cos($AzimInRad);
          $Y[$i] = $Y[$i - 1] + $_POST['nmbStep'] * sin($ZenHInRad) * sin($AzimInRad);
          $HV[$i] = $HV[$i - 1] + $_POST['nmbStep'] * cos($ZenHInRad);
          $Dlt[$i] = sqrt(pow($X[$i] - $X[0], 2) + pow($Y[$i] - $Y[0], 2));
          $i++;
        }
      }
      $i = 1;
      for ($h = $hInit; $h <= $_POST['nmbDepth']; $h += $_POST['nmbStep']) {
        $_POST['nmbZenDeg'.$h] = isset($_POST['nmbZenDeg'.$h]) ? $_POST['nmbZenDeg'.$h] : 0;
        $_POST['nmbZenMin'.$h] = isset($_POST['nmbZenMin'.$h]) ? $_POST['nmbZenMin'.$h] : 0;
        $_POST['nmbAzim'.$h] = isset($_POST['nmbAzim'.$h]) ? $_POST['nmbAzim'.$h] : 0; ?>
          <tr>
            <td><?php echo $i; ?></td><td><?php echo $h; ?></td>
            <td style="font-size: 100%;">
              <input style="width: 40px; font-size: 100%;" type="number" min=0 max=90
                   name="nmbZenDeg<?php echo $h; ?>" required
                   value="<?php echo $_POST['nmbZenDeg'.$h]; ?>" >&deg; &nbsp;
              <input style="width: 40px; font-size: 100%;" type="number" min=0 max=60
                   name="nmbZenMin<?php echo $h; ?>" required
                   value="<?php echo $_POST['nmbZenMin'.$h]; ?>" >&prime;
             </td>
             <td style="font-size: 100%;">
              <input style="width: 50px; font-size: 100%;" type="number" min=0 max=360
                   name="nmbAzim<?php echo $h; ?>" required
                   value="<?php echo $_POST['nmbAzim'.$h]; ?>" >&deg;
             </td>
             <td><?php if (isset($X[$i])) echo round($X[$i], 0); ?></td>
             <td><?php if (isset($Y[$i])) echo round($Y[$i], 0); ?></td>
             <td><?php if (isset($Dlt[$i])) echo round($Dlt[$i], 0); ?></td>
             <td><?php if (isset($HV[$i])) echo round($HV[$i], 1); ?></td>
             <td><?php if (isset($HV[$i])) echo round($h, 1) - round($HV[$i], 1); ?></td>
          </tr><?php $i++;
      } ?>
        </tbody>
        <tfoot>
          <tr><td colspan=4>
              <input style="font-size: 120%;" type="submit" name="sbtCalc" value="Обробити">
            </td>
					<td colspan=5></td></tr>
				</tfoot>
			</table>
		</form>
	</article>
</body>
</html>