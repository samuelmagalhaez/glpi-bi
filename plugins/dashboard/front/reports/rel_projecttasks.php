<?php

include ("../../../../inc/includes.php");
include ("../../../../inc/config.php");
include "../inc/functions.php";

global $DB;

Session::checkLoginUser();
Session::checkRight("profile", READ);

if(!empty($_POST['submit']))
{
    $data_ini =  $_POST['date1'];
    $data_fin = $_POST['date2'];
}

else {
    $data_ini = date("Y-01-01");
    $data_fin = date("Y-m-d");
    }

if(!isset($_POST["sel_pro"])) {
    $id_pro = $_GET["pro"];
}

else {
    $id_pro = $_POST["sel_pro"];
}

# entity
$sql_e = "SELECT value FROM glpi_plugin_dashboard_config WHERE name = 'entity' AND users_id = ".$_SESSION['glpiID']."";
$result_e = $DB->query($sql_e);
$sel_ent = $DB->result($result_e,0,'value');

if($sel_ent == '' || $sel_ent == -1) {
	$sel_ent = 0;
	$entidade = "";	
}
else {
	$entidade = "AND glpi_projects.entities_id IN (".$sel_ent.") ";
}

?>

<html>

<head>
	<title> GLPI - <?php echo _n('Project task', 'Project tasks',2); ?> </title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="content-language" content="en-us" />
	<meta charset="utf-8">

	<link rel="icon" href="../img/dash.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />
	<link href="../css/styles.css" rel="stylesheet" type="text/css" />
	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="../css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
	<link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />
	<script language="javascript" src="../js/jquery.min.js"></script>
	<link href="../inc/select2/select2.css" rel="stylesheet" type="text/css">
	<script src="../inc/select2/select2.js" type="text/javascript" language="javascript"></script>

	<script src="../js/bootstrap-datepicker.js"></script>
	<link href="../css/datepicker.css" rel="stylesheet" type="text/css">

	<script src="../js/media/js/jquery.dataTables.min.js"></script>
	<link href="../js/media/css/dataTables.bootstrap.css" type="text/css" rel="stylesheet" />
	<script src="../js/media/js/dataTables.bootstrap.js"></script>

	<script src="../js/extensions/Buttons/js/dataTables.buttons.min.js"></script>
	<script src="../js/extensions/Buttons/js/buttons.html5.min.js"></script>
	<script src="../js/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
	<script src="../js/extensions/Buttons/js/buttons.print.min.js"></script>
	<script src="../js/media/pdfmake.min.js"></script>
	<script src="../js/media/vfs_fonts.js"></script>
	<script src="../js/media/jszip.min.js"></script>

	<script src="../js/extensions/Select/js/dataTables.select.min.js"></script>
	<link href="../js/extensions/Select/css/select.bootstrap.css" type="text/css" rel="stylesheet" />

	<style type="text/css">
		select {
			width: 60px;
		}

		table.dataTable {
			empty-cells: show;
		}

		a:link,
		a:visited,
		a:active {
			text-decoration: none;
		}
	</style>

	<?php echo '<link rel="stylesheet" type="text/css" href="../css/style-'.$_SESSION['style'].'">';  ?>

</head>

<body style="background-color: #e5e5e5; margin-left:0%;">

	<div id='content'>
		<div id='container-fluid' style="margin: <?php echo margins(); ?> ;">
			<div id="charts" class="fluid chart">
				<div id="pad-wrapper">
					<div id="head-rel" class="fluid">

						<style type="text/css">
							a:link,
							a:visited,
							a:active {
								text-decoration: none
							}

							a:hover {
								color: #000099;
							}
						</style>

						<a href="../index.php"><i class="fa fa-home"
								style="font-size:14pt; margin-left:25px;"></i><span></span></a>

						<div id="titulo"> <?php echo _n('Project task', 'Project tasks',2); ?> </div>

						<div id="datas-tec3" class="span12 fluid">
							<form id="form1" name="form1" class="form_rel" method="post"
								action="./rel_projects.php?con=1" style="margin-left: 37%;">
								<table border="0" cellspacing="0" cellpadding="3" bgcolor="#efefef">
									<tr>
										<td style="width: 310px;">
											<?php
				$url = $_SERVER['REQUEST_URI'];
				$arr_url = explode("?", $url);
				$url2 = $arr_url[0];
				?>
											<script language="Javascript">
												$('#dp1').datepicker('update');
												$('#dp2').datepicker('update');
											</script>
										</td>
										<td style="margin-top:2px;"></td>
									</tr>
									<tr>
										<td height="15px"></td>
									</tr>
									<tr></tr>
								</table>
								<?php Html::closeForm(); ?>
								<!-- </form> -->

						</div>
					</div>
				</div>

				<script type="text/javascript">
					$(document).ready(function () {
						$("#sel1").select2({
							dropdownAutoWidth: true
						});
					});
				</script>

				<?php

if(isset($_GET['sel_pro'])) {

$pro_id = $_GET['sel_pro'];

//if($con == "1") {

if(!isset($_POST['date1']))
{
    $data_ini2 = $_GET['date1'];
    $data_fin2 = $_GET['date2'];
}

else {
    $data_ini2 = $_POST['date1'];
    $data_fin2 = $_POST['date2'];
}

if($data_ini2 === $data_fin2) {
    $datas2 = "LIKE '".$data_ini2."%'";
}

else {
    $datas2 = "BETWEEN '".$data_ini2." 00:00:00' AND '".$data_fin2." 23:59:59'";
}


//Project
$sql_pro =
"SELECT glpi_projects.id, glpi_projects.name,glpi_entities.name as entidade
FROM glpi_projects
LEFT JOIN glpi_entities ON glpi_projects.entities_id = glpi_entities.id
WHERE glpi_projects.id = ".$pro_id."
".$entidade."
ORDER BY date DESC ";

$result_pro = $DB->query($sql_pro);
$project = $DB->result($result_pro,0,'name');
$ent_projeto = $DB->result($result_pro,0,'entidade');

// tasks
$sql_cham =
"SELECT * 
FROM glpi_projecttasks
WHERE glpi_projecttasks.projects_id = ".$pro_id."
".$entidade."
ORDER BY date DESC ";

$result_cham = $DB->query($sql_cham);

$conta_cons = $DB->numrows($result_cham);
$consulta = $conta_cons;

//tempo de todas as tarefas
while($row = $DB->fetchAssoc($result_cham)){
    $tempo_total += $row['effective_duration'];
}
// transforma tempo em segundos para h:m
function converterHora($hora){
	$min = gmdate("i", $hora);    
	$hora = (gmdate("d", $hora)-1)*24 + gmdate("H", $hora);  
	return $hora.'h : '.$min.'m';
}

echo "
	<div class='well info_box fluid col-md-12 report' style='margin-left: -1px;'>
	
	<table class='fluid' style='font-size: 18px; font-weight:bold; margin-bottom: 30px;' cellpadding = 1px>
		<tr>
			<td style='color: #000;' id='nome_projeto'>". __('Project') .":  ". $project ."</td></tr>
			<tr><td style='color: #000;' id='ent_projeto'> Entidade: ".$ent_projeto." </td>			
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td style='vertical-align:middle; width:350px;'> <span style='color: #000;'>"._n('Task', 'Tasks',2).": </span>". $conta_cons ."</td>
			<td style='vertical-align:middle; width:350px;'> <span style='color: #000;'>".__('Time').": </span><span id='tempo_tasks'>".converterHora($tempo_total)."</span></td>			
		</tr>
	</table>";

	echo "

	<div class='row'>
	<div class='col-md-4 append_filtro' style='float:none;margin:auto;'></div>
	
	
		
		</div>
	<table id='tarefa' class='display' style='font-size: 13px; font-weight:bold;' cellpadding = 2px>
		<thead>
			<tr>
				<th style='text-align:center; cursor:pointer;'> ". __('ID') ."  </th>				
				<th style='text-align:center; cursor:pointer;'> ". __('Name') ."  </th>
				<th style='text-align:center; cursor:pointer;'> ". __('Technician') ."  </th>
				<th style='text-align:center; cursor:pointer;'> ". __('Creation date') ." </th>				
				<th style='text-align:center; cursor:pointer;'> ". __('Begin') ."</th>
				<th style='text-align:center; cursor:pointer;'> ". __('End') ." </th>
				<th style='text-align:center; cursor:pointer;'> ". __('Duration') ." </th>		
				<th style='text-align:center; cursor:pointer;'> ". __('Progress') ."</th>						
			</tr>
		</thead>
	<tbody>
	";

//listar projetos

$DB->dataSeek($result_cham, 0);
while($row = $DB->fetchAssoc($result_cham)){
	
	//percent done		
	$barra = $row['percent_done'];
	
	// cor barra
	if($barra == 100) { $cor = "progress-bar-success"; }
	if($barra >= 80 and $barra < 100) { $cor = " "; }
	if($barra > 51 and $barra < 80) { $cor = "progress-bar-warning"; }
	if($barra > 0 and $barra <= 50) { $cor = "progress-bar-danger"; }
	if($barra < 0) { $cor = "progress-bar-danger"; $barra = 0; }
	
	

	echo "
	<tr>
	<td style='text-align:center; vertical-align:middle;'><a href=".$CFG_GLPI['url_base']."/front/projecttask.form.php?id=". $row['id'] ." target=_blank >" . $row['id'] . "</a></td>		
	<td style='text-align:center; vertical-align:middle;'> ". $row['name'] ." </td>
	<td style='text-align:center; vertical-align:middle;'> ". getUserName($row['users_id']) ." </td>				
	<td style='text-align:center; vertical-align:middle;'> ". conv_data_hora($row['date']) ."</td>
	<td style='text-align:center; vertical-align:middle;'> ". conv_data_hora($row['real_start_date']) ."</td>
	<td style='text-align:center; vertical-align:middle;'> ". conv_data_hora($row['real_end_date']) ."</td>
	<td style='text-align:center; vertical-align:middle;'> ". converterHora($row['effective_duration']) ."</td>
	<td style='text-align:center; vertical-align:middle;'> 
		<div class='progress' style='margin-top: 5px; margin-bottom: 5px;'>
			<div class='progress-bar ". $cor ." ' role='progressbar' aria-valuenow='".$barra."' aria-valuemin='0' aria-valuemax='100' style='width: ".$barra."%;'>
			 			".$barra." % 	
			 </div>		
		</div>			
	</td>
	</tr>";
}

echo "</tbody>
		</table>
		</div>"; 
		
		
		?>

				<script type="text/javascript" charset="utf-8">
				
					$('#tarefa')
						.removeClass('display')
						.addClass('table table-striped table-bordered table-hover dataTable');

					function sumDurations(durations) {
						return durations.reduce((sum, string) => {
							var mins, secs;
							[mins, secs] = string.split(":").slice(-2).map(n => parseInt(n, 10));
							return sum + mins * 60 + secs;
						}, 0);
					}

					function formatDuration(duration) {
						function pad(number) {
							return `${number}`.slice(-2);
						}
						duration = duration * 60;
						let hours = duration / 3600 | 0;
						let minutes = duration % 3600 / 60 | 0;
						let minsSecs = `${pad(minutes)}`;
						return hours > 0 ? `${hours}h${minsSecs}m` : minsSecs;
					}

					$(document).ready(function () {
						$('#tarefa').DataTable({
							language: {
								"url": "../js/Portuguese-Brasil.json"
							},
							select: true,
							dom: 'Blfrtip',

							// altera o valor da soma das durações das tasks de acordo com o filtro
							drawCallback: function (settings) {
								
								var nomepag = $("#nome_projeto").html() + '\n' + $("#ent_projeto").html();
								$("title").html(nomepag);
								var array = [];
								this.api().column(6, {
									search: 'applied'
								}).data().each(function (value, index) {
									array.push(value.replace('h ', '').replace(': ', ':').replace('m',
									''));
								});
								$("#tempo_tasks").empty().html(formatDuration(sumDurations(array)));
								

							},
							pagingType: "full_numbers",
							sorting: [
								[0, 'desc'],
								[1, 'desc'],
								[2, 'desc'],
								[3, 'desc'],
								[4, 'desc'],
								[5, 'desc'],
								[6, 'desc'],
								[7, 'desc']
							],
							displayLength: 25,
							lengthMenu: [
								[25, 50, 75, 100],
								[25, 50, 75, 100]
							],
							buttons: [{
									extend: "copyHtml5",
									text: "<?php echo __('Copy'); ?>"
								},
								
								{
									extend: "collection",
									text: "<?php echo _x('button', 'Export'); ?>",
									buttons: ["csvHtml5",
									{
										extend: "excelHtml5",
										orientation: "landscape",
										message: "<tr><th>"+$('#nome_projeto').html()+"</th></tr>",
									},
									
										{
											extend: "pdfHtml5",
											orientation: "landscape",
											// message: 'Tempo: '+ $('#tempo_tasks').html(),
										}
									]
								}
							],

							// monta os filtros dos tecnicos
							initComplete: function () {
								
								this.api().columns(2).every(function () {
									var column = this;
									$('.dataTables_filter').remove();
									$(".append_filtro").append(
										"<label style='font-size:22px;'>Executor:</label>");
									var select = $(
											'<select class="form-control" style="margin: 5px auto 20px auto;height:40px !important;"><option value=""></option></select>'
										)
										.appendTo(".append_filtro")
										.on('change', function () {
											var val = $.fn.dataTable.util.escapeRegex(
												$(this).val()
											);

											column
												.search(val ? '^' + val + '$' : '', true, false)
												.draw();
										});


									column.data().unique().sort().each(function (d, j) {
										select.append('<option value="' + d + '">' + d +
											'</option>');

									});
								});
							}

						});

					});
				</script>
				<?php
echo '</div><br>';
}

else {

	echo "
	<div id='nada_rel' class='well info_box fluid col-md-12'>
	<table class='table' style='font-size: 18px; font-weight:bold;' cellpadding = 1px>
	<tr><td style='vertical-align:middle; text-align:center;'> <span style='color: #000;'>" . __('No ticket found', 'dashboard') . "</td></tr>
	<tr></tr>
	</table></div>";

}

?>

			</div>
		</div>
	</div>
</body>

</html>