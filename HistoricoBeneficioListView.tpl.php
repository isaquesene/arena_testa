<?php
$this->assign('title', 'ARENA | HistoricoBeneficios');
$this->assign('nav', 'historicobeneficios');

$this->display('_Header.tpl.php');
?>

<?php
include('conectar.php');
$res = mysqli_query($conn, "SELECT s.id_socio,s.nome,t.descricao,s.email,s.send,s.open,p. * FROM socio s 
INNER JOIN pagamento p ON s.id_socio = p.id_socio 
INNER JOIN time t ON p.id_time = t.id_time");
?>



<div class="container">

	<!--filter table-->
	<h1>
		<i class="icon-th-list"></i> Enviar beneficio
		<span class='input-append pull-right searchContainer'>
			<!--<input id='myInput' onkeyup="myFunction()" type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>-->
			<select id='filtro' onkeyup="myFunction()">
				<option value="todos">todos</option>
				<?php
				include('conectar.php');

				// $id_empresa = $_GET['id_empresa']; 
				$sql = "select id_time, descricao from time";
				$result = mysqli_query($conn, $sql);

				while ($row = mysqli_fetch_assoc($result)) {

					?>
					<option value="<?php echo $row['id_time']; ?>"><?php echo $row['descricao']; ?></option>
				<?php } ?>
			</select>
			<button class='btn' onclick="myFunction()"><i class="icon-search"></i></button>
		</span>

	</h1>

	<!--tabela select inner join-->
	
	<?php if (mysqli_num_rows($res) > 0) { ?>
		<table class="collection table table-bordered table-hover" id="myTable">
			<thead>
				<tr>
					<!--<th>S.No</th>-->
					<th>Check Socio</th>
					<th>Socio</th>
					<th>Email</th>
					<th>Modalidade</th>
					<th>E-mail</th>
					<th>Status do Envio</th>
				</tr>
			</thead>
			<tbody id="post_data">
				<?php
				//$i=1;
				while ($row = mysqli_fetch_assoc($res)) { ?>
					<tr>
						<td>
						<input type="checkbox" name="check_socios[]" value="<?php echo $row['id_socio'] ?>">
						</td>
						<td>
							<?php echo $row['nome'] ?>
						</td>
						<td>
							<?php echo $row['email'] ?>
						</td>
						<td>
							<?php echo $row['descricao'] ?>
						</td>
						<td id="btn<?php echo $row['id_socio'] ?>">
							<!--send & open-->

							<button type="button" class="btn btn-success"
								onclick="send_msg('<?php echo $row['id_socio'] ?>')">Enviar</button>

						</td>
						<td>
							<?php
							if ($row['send'] == 1) {
								echo "Sim";
							} else {
								echo "Não";
							}
							?>
						</td>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>

	<?php } else {
		echo "No data found";
	} ?>
	<button id="btn_all" onclick="send_msg_all()">Enviar para todos</button>
</div>



<!--Add checkbox-->


<div class="container">
	<!--tabela beneficio-->
	<span class='input-append pull-right searchContainer'>
		<input id='myInputSocioBeneficio' onkeyup="myFunctionSocioBeneficio()" type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
	<div class="modal-header">
		<h5 id="exampleModalLabel">Tabela Socio Beneficio </h5>
	</div>

		<table class="collection table table-bordered table-hover" id="myTableSocioBeneficio">
			<thead>
				<tr>
					<th scope="col">Beneficio</th>
				</tr>
			</thead>
			<tbody id="post_data">
				<?php
				require_once("conectar.php");

				/*$sql = "SELECT b.nome_beneficio,s.nome,h.* FROM beneficio b INNER JOIN 
				historico_beneficio h ON b.id_beneficio = h.id_beneficio INNER JOIN 
				socio s ON h.id_socio = s.id_socio;";*/
				$sql = "SELECT id_beneficio, nome_beneficio FROM beneficio";
				$result = mysqli_query($conn, $sql);
				while ($row = mysqli_fetch_assoc($result)) {
					?>
					<tr>
						<td>
							<label>
								<?php echo $row['nome_beneficio'] ?>
								<input type="checkbox" name="check_beneficios[]" value="<?php echo $row['id_beneficio'] ?>">
							</label>
						</td>
						<?php
				}

				?>
			</tbody>
		</table>
		<!--button add checkbox-->
		<button onClick="getCheckBoxesAndSubmit()" name="box">Add checkbox</button>
		<span id="req_result">Teste</span>
	
	<!--BUTTON ADD MODAL-->
	<!--<a href="templates/HistoricoBeneficioAdd.tpl"><button class="btn btn-primary">Cadastrar Beneficio</button></a>-->
</div>





<!--script envio de e-mail-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
	//confirmação de envoi de e-mail
	function send_msg(id) {
		var check = confirm('Enviando e-mail, clique "OK" para continuar! ');
		if (check == true) {
			jQuery('#btn' + id).html('Por favor, aguarde...');
			jQuery.ajax({
				url: 'templates/email.php',
				type: 'post',
				data: 'id_socio=' + id,
				success: function (result) {
					result = jQuery.parseJSON(result);
					console.log(result.status);
					if (result.status == true) {
						jQuery('#btn' + id).html('Enviado');
					}
					if (result.status == false) {
						jQuery('#btn' + id).html('<button type="button" class="btn btn-success" onclick=send_msg("' + id + '")>Send</button><div class="error_msg">' + result.msg + '</div>');
					}
				}
			});
		}
	}


	//Envio de e-mail em massa
	function send_msg_all() {
		var check = confirm('Enviar email em massa, clique "OK" para continuar! ');
		if (check == true) {

			var filtro = jQuery('#filtro').val();

			jQuery('#btn_all').html('Por favor, aguarde...');
			jQuery.ajax({
				url: 'templates/email.php',
				type: 'post',
				data: 'id_socio=all&filtro=' + filtro,
				success: function (result) {
					result = jQuery.parseJSON(result);
					if (result.status == true) {
						jQuery('#btn').html('Enviado');
					}
					if (result.status == false) {
						jQuery('#btn' + id).html('<button type="button" class="btn btn-success" onclick=send_msg("' + id + '")>Send</button><div clsss="error_msg">' + result.msg + '</div>');
					}
				}
			});
		}
	}
</script>



<!--filter search table enviar beneficio-->
<script type="text/javascript">
	function myFunction() {
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput");
		filter = input.value.toUpperCase();
		table = document.getElementById("myTable");
		tr = table.getElementsByTagName("tr");

		// Loop through all table rows, and hide those who don't match the search query
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[2];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
			}
		}
	}
</script>

<!--filter search table socio beneficio-->
<script type="text/javascript">
	function myFunctionSocioBeneficio() {
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInputSocioBeneficio");
		filter = input.value.toUpperCase();
		table = document.getElementById("myTableSocioBeneficio");
		tr = table.getElementsByTagName("tr");

		// Loop through all table rows, and hide those who don't match the search query
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[1];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
			}
		}
	}


	//Teste
	function getCheckBoxesAndSubmit(){
		
		//pegando checkboxes marcadas de sócios
		var socios = document.getElementsByName('check_socios[]');
		var len = socios.length;
		var check_socios_array = [];
		
		
		for (var i=0; i<len; i++) {                                                                                
			if (socios[i].checked){
				check_socios_array.push(socios[i].value);
			}
		}
		
		//pegando checkboxes marcadas de benefícios
		var beneficios = document.getElementsByName('check_beneficios[]');
		var len2 = beneficios.length;
		var check_beneficios_array = [];

		for (var i=0; i<len2; i++) {                                                                                
			if (beneficios[i].checked){
				check_beneficios_array.push(beneficios[i].value);
			}
		}	
		
	
		//enviando requisição via AJAX
		jQuery.ajax({
			url: 'templates/HistoricoBeneficioAdd.tpl.php',
			type: 'post',
			data: 'tipo=checkboxes&socios=' + check_socios_array + '&beneficios=' + check_beneficios_array,
			success: function (result) {

				result = jQuery.parseJSON(result);	

				jQuery('#req_result').html(result.msg);				
				
			}
		});	
		
	}   

	//fim teste


</script>




<?php
$this->display('_Footer.tpl.php');
?>