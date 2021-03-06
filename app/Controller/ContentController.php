<?php
App::uses('File', 'Utility');

class ContentController extends AppController {
	public $name = 'Content'; // Nome do controller
	public $uses = array(''); // Model usado pelo controller
	public $helpers = array('Html', 'Form', 'Js'); // Helpers usados pela view

	// Método de funções carregadas antes de qualquer coisa
	function beforeFilter() {
		parent::beforeFilter();
	}

	// Função para o gerenciamento de paginas
	function pages() {
		if($this->OTAAC->authAdmin()) { // Componente de autorização
			$pages = array(); // Cria um array de dados vazio
	        $library = '..'.DS.'View'.DS.'Themed'.DS.themeAAC.DS.'Pages'.DS;
			foreach(glob($library.'*.ctp') as $page) { // Percorre as páginas existentes em View/Pages
				$page = str_replace(array($library, '.ctp'), '', $page); // Pega o nome da página
				$pages[] = ucwords($page); // Guarda em um array
			}

			$this->set(compact('pages')); // Seta para a view
		}
	}

	// Função para criar as páginas
	function create_page() {
		if($this->OTAAC->authAdmin()) { // Componente de autorização
			if (!empty($this->data)) { // Se foi passado algo pelo formulário:
				$library 	= '..'.DS.'View'.DS.'Themed'.DS.themeAAC.DS.'Pages'.DS; // Local do arquivo

				$page 		= strtolower($this->data['Page']['title']); // Arruma o título do arquivo
				$content 	= $this->data['Page']['body']; // Arruma o corpo do arquivo

				$file 		= new File($library.$page.'.ctp', true, 0644); // Cria o arquivo
				$file->write($content); // Escreve no arquivo
				$file->close(); // Fecha o arquivo

				return $this->redirect(array('action' => 'pages')); // Retorna verdadeiro (redireciona)
			}

			$this->render('page_form');
		}
	}

	// Função para editar as páginas
	function edit_page($page) {
		if($this->OTAAC->authAdmin()) { // Componente de autorização
			$library 	= '..'.DS.'View'.DS.'Themed'.DS.themeAAC.DS.'Pages'.DS; // Local do arquivo

			if (!empty($this->data)) { // Se foi passado algo pelo formulário:
				$page 		= strtolower($this->data['Page']['title']); // Arruma o título do arquivo
				$content 	= $this->data['Page']['body']; // Arruma o corpo do arquivo

				$file 		= new File($library.$page.'.ctp', true, 0644); // Cria o arquivo
				$file->write($content); // Escreve no arquivo
				$file->close(); // Fecha o arquivo

				return $this->redirect(array('action' => 'pages')); // Retorna verdadeiro (redireciona)
			} else {
				$title 		= $page;
				$page 		= strtolower($page);
				$file 		= new File($library.$page.'.ctp');
				$content 	= $file->read();

				$this->request->data['Page']['title'] 	= $title;
				$this->request->data['Page']['body'] 	= $content;
			}

			$this->render('page_form');
		}
	}

	// Função para deletar as páginas
	function delete_page($page) {
		if($this->OTAAC->authAdmin()) { // Componente de autorização
			$library 	= '..'.DS.'View'.DS.'Themed'.DS.themeAAC.DS.'Pages'.DS; // Local do arquivo
			$page 		= strtolower($page);
			$file 		= new File($library.$page.'.ctp');

			$file->delete();

			return $this->redirect(array('action' => 'pages')); // Retorna verdadeiro (redireciona)
		}
	}

	// Método que monta o menu library dinamicamente
	function library() {
		$this->autoRender = false; // Uma pagina que não é renderizada
		$data = array(); // Cria um array de dados vazio
        $library = '..'.DS.'View'.DS.'Themed'.DS.themeAAC.DS.'Pages'.DS;
		foreach(glob($library.'*.ctp') as $page) { // Percorre as páginas existentes em View/Pages
			$page = str_replace(array($library, '.ctp'), '', $page); // Pega o nome da página
			$data['pages'][] = $page; // Guarda em um array
		}
		return $data; // Seta para a view
	}
}
