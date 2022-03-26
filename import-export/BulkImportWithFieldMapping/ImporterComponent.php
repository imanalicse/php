<?php
namespace AttendeeImport\Controller\Component;

use Cake\Event\Event;

class ImporterComponent extends BaseComponent
{
    var $controller;
    var $Session;

    function startup(Event $event)
    {
        $this->controller = $this->_registry->getController();
        $this->Session = $this->controller->getRequest()->getSession();
    }

    public function saveImportFile($uploadFileName)
    {
        $import_file = [
            'name' => $uploadFileName,
            'user_id' => $this->authUser('id')
        ];

        $this->ImportedFiles = $this->getDbTable('AttendeeImport.ImportedFiles');
        $ImportFile = $this->ImportedFiles->newEmptyEntity();
        $ImportFile = $this->ImportedFiles->patchEntity($ImportFile, $import_file);
        if ($this->ImportedFiles->save($ImportFile)) {
            return $ImportFile->id;
        }
    }

    public function getLastImportFileInfo() {
        $importedFilesModel = $this->getDbTable('AttendeeImport.ImportedFiles');
        $imported_file = $importedFilesModel->find()->order(['id'=>'DESC'])->enableHydration(false)->first();
        return $imported_file;
    }

    public function getSampleFileInfo() {
        $sample_file_info = [];
        if (empty($sample_file_info)) {
            $file_name = 'sample.xlsx';
            $file_path = WWW_ROOT. DS . 'uploads' .DS. '_default' . DS . 'invitation-import' . DS . $file_name;
            if (file_exists($file_path)) {
                $sample_file_info = [
                    'file_name' => $file_name,
                    'file_path' => $file_path
                ];
            }
        }

        return $sample_file_info;
    }
}
