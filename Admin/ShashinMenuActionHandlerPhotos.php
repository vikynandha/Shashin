<?php

class Admin_ShashinMenuActionHandlerPhotos {
    private $functionsFacade;
    private $menuDisplayer;
    private $adminContainer;
    private $request;

    public function __construct(
      ToppaFunctionsFacade $functionsFacade,
      Admin_ShashinMenuDisplayer $menuDisplayer,
      Admin_ShashinContainer $adminContainer,
      array $request) {
        $this->functionsFacade = $functionsFacade;
        $this->menuDisplayer = $menuDisplayer;
        $this->adminContainer = $adminContainer;
        $this->request = $request;
    }

    public function run() {
        try {
            if ($this->request['switchingFromAlbumsMenu']) {
                $this->functionsFacade->checkAdminNonceFields("shashinNoncePhotosMenu_" . $this->request['id']);
            }

            if ($this->request['shashinAction'] == 'updateIncludeInRandom') {
                $this->functionsFacade->checkAdminNonceFields("shashinNonceUpdate", "shashinNonceUpdate");
                $message = $this->runUpdateIncludeInRandom();
            }

            echo $this->menuDisplayer->run($message);
        }

        catch (Exception $e) {
            echo "<p>" . __("Shashin Error: ", "shashin") . $e->getMessage() . "</p>";
        }

        return true;
    }

    public function runUpdateIncludeInRandom() {
        $photoCollection = $this->adminContainer->getClonablePhotoCollection();
        $photoCollection->setLimitNeeded(false);
        $shortcodeMimic = array('id' => $this->request['id'], 'type' => 'albumphotos');
        $photoCollection->setProperties($shortcodeMimic);
        $photos = $photoCollection->getCollection();

        foreach ($this->request['includeInRandom'] as $k=>$v) {
            $photoData = array('includeInRandom'=> $v);
            $photos[$k]->set($photoData);
            $photos[$k]->flush();
        }

        return __('Updated "Include In Random" settings', "shashin");
    }
}
