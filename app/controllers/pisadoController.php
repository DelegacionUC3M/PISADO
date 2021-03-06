<?php

class pisadoController extends Controller {

	function createAction() {
		$this->security(false);
		$data = array();

		$data['titulaciones'] = DBDelegados::getTitulaciones();
		$pisado = new Pisado;
		$pisado->id_titulacion = $_SESSION['user']->id_titulacion;
		$data['pisado'] = $pisado;

		if (isset($_POST['titulacion']) && isset($_POST['asignatura']) && isset($_POST['curso']) && isset($_POST['grupo'])
			&& isset($_POST['profesor']) && isset($_POST['texto'])) {

			$pisado->nia = $_SESSION['user']->nia;
			$pisado->email = $_SESSION['user']->email;
			$pisado->autor = $_SESSION['user']->name;
			$pisado->id_titulacion = (int) $_POST['titulacion'];
			$pisado->asignatura = htmlspecialchars($_POST['asignatura']);
			$pisado->curso = htmlspecialchars($_POST['curso']);
			$pisado->grupo = (int) $_POST['grupo'];
			$pisado->profesor = htmlspecialchars($_POST['profesor']);
			$pisado->texto = htmlspecialchars($_POST['texto']);
			$pisado->id_group = 0;

			if(!empty($_POST['titulacion']) && !empty($_POST['asignatura']) && !empty($_POST['curso'])
				&& !empty($_POST['grupo']) && !empty($_POST['texto'])) {
				if(is_numeric($_POST['grupo'])) {

					if($pisado->save()) {
						/*$destinatarios = array();
						$cuerpo = $this->render_email('CreateU', array('pisado' => $pisado));
						$destinatarios[] = array('nia' => $pisado->nia);
						$this->send('¡Has creado un nuevo PISADO!', $destinatarios, $cuerpo);

						$cuerpo = $this->render_email('Pisado', array('pisado' => $pisado));
						$destinatarios = DBDelegados::findDelegadosCurso($pisado->id_titulacion,$pisado->curso);
						$delegadosTit = DBDelegados::findDelegadosTitulacion($pisado->id_titulacion);
						foreach ($delegadosTit as $delegado) {
							$destinatarios[] = $delegado;
						}
						$this->send('¡Hay un nuevo PISADO para ti!', $destinatarios, $cuerpo);*/

						header('Location: /pisado/inicio'); die();
					} else {
						$data['error'] = 'Ha ocurrido un error con la base de datos. Inténtelo de nuevo.';
					}
				} else {
					$data['error'] = 'El grupo debe ser un numero.';
				}
			} else {
				$data['error'] = 'Debes rellenar todos los campos.';
			}

			$this->render('create', $data);
		} else {
			$this->render('create', $data);
		}
	}

	function closeAction() {
		$this->security(false);

		$id = (isset($_GET['id'])) ? (int) $_GET['id'] : false;
		$pisado = Pisado::findById($id);
		$data = array();

		if ($pisado) {
			if (($pisado->nia == $_SESSION['user']->nia) || (($pisado->id_titulacion == $_SESSION['user']->id_titulacion) && $_SESSION['user']->isDelegadoCurso()) || ($_SESSION['user']->isDelegadoCentro()) ) {
				$archive = new Archive;
				$archive->pisado = $pisado;
				if($archive->save()) {
					header('Location: /pisado/inicio');
				} else {
					$data['archive_error'] = 'No se ha podido archivar este pisado';
					$this->render('view', $data);
				}
			} else {
				$this->render_error(401);
			}
		} else {
			$this->render_error(404);
		}
	}

	function openAction() {
		$this->security(false);

		$id = (isset($_GET['id'])) ? (int) $_GET['id'] : false;
		$pisado = Pisado::findById($id);
		$data = array();

		if ($pisado) {
			if (($pisado->nia == $_SESSION['user']->nia) || (($pisado->id_titulacion == $_SESSION['user']->id_titulacion) && $_SESSION['user']->isDelegadoCurso()) || ($_SESSION['user']->isDelegadoCentro()) ) {
				$archive = Archive::findByPisado($pisado->id);
				if(isset($archive) && $archive->delete()) {
					header('Location: /pisado/inicio');
				} else {
					$data['archive_error'] = 'No se ha podido archivar este pisado';
					$this->render('view', $data);
				}
			} else {
				$this->render_error(401);
			}
		} else {
			$this->render_error(404);
		}
	}

	function viewAction() {
		$this->security(false);

		$id = (isset($_GET['id'])) ? (int) $_GET['id'] : false;
		$pisado = Pisado::findById($id);
		$data = array();

		if ($pisado) {
			if (($pisado->nia == $_SESSION['user']->nia) || (($pisado->id_titulacion == $_SESSION['user']->id_titulacion) && $_SESSION['user']->isDelegadoCurso()) || ($_SESSION['user']->isDelegadoCentro()) ) {//dentro de view hay que controlar que no muestre los datos.

				if (isset($_POST['comment'])) {
					if (empty($_POST['comment'])) {
						$data['error'] = 'Debes introducir texto en tu comentario';
					} else {
						$comentario = new ComentarioPisado;

						$comentario->id_pisado = $pisado->id;
						$comentario->nia = $_SESSION['user']->nia;
						$comentario->text = htmlspecialchars($_POST['comment']);
						if ($_SESSION['user']->isDelegado && $pisado->nia != $_SESSION['user']->nia) {
							if ($_SESSION['user']->isDelegadoCentro()) {
								$cargo = 'Delegado de Centro';
							} else if ($_SESSION['user']->isDelegadoTitulacion()) {
								$cargo = 'Delegado de Titulación';
							} else {
								$cargo = 'Delegado de Curso';
							}
							$comentario->nombre = $_SESSION['user']->name.' ('.$cargo.')';
						} else {
							$comentario->nombre = '';
						}

						if (!$comentario->save()) {
							$data['error'] = 'Ha ocurrido un error al guardar el comentario. Inténtelo de nuevo.';
						} else {
							/*$cuerpo = $this->render_email('Comentario',array('pisado' => $pisado));
							$destinatarios = DBDelegados::findDelegadosCurso($pisado->id_titulacion,$pisado->curso);
							$delegadosTit = DBDelegados::findDelegadosTitulacion($pisado->id_titulacion);
							foreach ($delegadosTit as $delegado) {
								$destinatarios[] = $delegado;
							}
							$destinatarios[] = array('nia' => $pisado->nia);
							$this->send('¡Tienes un nuevo comentario en un PISADO!',$destinatarios,$cuerpo);*/
						}
					}
				}

				$comentarios = ComentarioPisado::findByIdpisado($pisado->id);
				$data['pisado'] = $pisado;
				$data['comentarios'] = $comentarios;
				$data['id'] = $pisado->id;
				$delegadoTitulacion = DBDelegados::findDelegadosTitulacion($pisado->id_titulacion);
				foreach ($delegadoTitulacion as $delegado) {
					$data['delegado'][] = array('email' => $delegado['nia'] . '@alumnos.uc3m.es', 'nombre' => $delegado['name'] . ' ' . $delegado['surname']);
				}
				$data['archive'] = (is_null(Archive::findByPisado($pisado->id))) ? false : true;


				$this->render('view', $data);
			} else {
				$this->render_error(401);
			}
		} else {
			$this->render_error(404);
		}
	}

}
