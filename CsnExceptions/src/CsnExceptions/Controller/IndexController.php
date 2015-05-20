<?php

namespace CsnExceptions\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use CsnExceptions\Entity\LogException;

class IndexController extends AbstractActionController {

    /**
	 * @var CsnUser ModuleOptions
	 */
	protected $options;

    /**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

    /**
	 * @var Zend\Mvc\I18n\Translator
	 */
	protected $translatorHelper;

    /**
	 * @var Zend\Form\Form
	 */
	protected $articleFormHelper;


    /**
	 * View action
	 *
	 * The method list all articles
	 *
	 * @return Zend\View\Model\ViewModel|array articles object
	 */
    public function indexAction() {
        /*$this->getResponse()->getHeaders()->addHeaders(array(
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Cache-Control' => 'post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
        ));*/

        $dql = "SELECT a FROM CsnCms\Entity\Article a ORDER BY a.created DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        //TODO Create Paginator
        $query->setMaxResults(30);
        $articles = $query->getResult();
        return new ViewModel(array('articles' => $articles));
    }

    // D - delete
    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('csn-cms', array('controller' => 'article', 'action' => 'index'));
        }

        try {
            //throw new \Exception('asdfasdf');
            $article = $this->getEntityManager()->find('CsnCms\Entity\Article', $id);
        } catch (\Exception $e) {
            $message = 'Something went wrong during Editing the story! Please, try again later.';
				return $this->getServiceLocator()
                            ->get( 'csncms_error_view' )
                            ->createErrorView(
                                $this->getTranslatorHelper()->translate(
                                    $message, 'csnuser' ),
                                $e,
                                $this->getOptions()->getDisplayExceptions() );
        }

        if ($article) {
            $this->filename = $article->getPhoto();
            $this->removeOldFiles($this -> uploadDir, $this -> avatarDir);
            //Delete the article
            $this->getEntityManager()->remove($article);
            $this->getEntityManager()->flush();
            return $this->redirect()->toRoute('csn-cms', array('controller' => 'article', 'action' => 'index'));
        }

        return $this->redirect()->toRoute('csn-cms', array('controller' => 'article', 'action' => 'index'));
    }
    
	/**
	 * get entityManager
	 *
	 * @return EntityManager
	 */
	private function getEntityManager()
	{
		if ( null === $this->entityManager ) {
			$this->entityManager = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
		}

		return $this->entityManager;
	}

    /**
	 * get translatorHelper
	 *
	 * @return  Zend\Mvc\I18n\Translator
	 */
	private function getTranslatorHelper()
	{
		if ( null === $this->translatorHelper ) {
			$this->translatorHelper = $this->getServiceLocator()->get( 'MvcTranslator' );
		}

		return $this->translatorHelper;
	}

    /**
	 * get options
	 *
	 * @return ModuleOptions
	 */
	private function getOptions()
	{
		if ( null === $this->options ) {
			$this->options = $this->getServiceLocator()->get( 'csnuser_module_options' );
		}

		return $this->options;
	}

}
