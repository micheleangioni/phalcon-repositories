<?php

namespace MicheleAngioni\PhalconRepositories;

trait MongoFix
{
    /**
     * <------ WORKAROUND TO FIX PHALCON INCUBATOR MONGO BUG
     * https://github.com/phalcon/incubator/issues/760
     */
    public function save()
    {
        if (version_compare(PHP_VERSION, '7.1.0') >= 0) {
            $this->_dependencyInjector = \Phalcon\Di::getDefault();
            $this->_modelsManager = $this->_dependencyInjector->getShared("collectionManager");
            $this->_modelsManager->initialize($this);
        }

        return parent::save();
    }

    public function delete()
    {
        if (version_compare(PHP_VERSION, '7.1.0') >= 0) {
            $this->_dependencyInjector = \Phalcon\Di::getDefault();
            $this->_modelsManager = $this->_dependencyInjector->getShared("collectionManager");
            $this->_modelsManager->initialize($this);
        }

        return parent::delete();
    }

    /**
     * WORKAROUND TO FIX PHALCON INCUBATOR MONGO BUG ------>
     */
}