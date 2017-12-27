<?php

namespace Zf2Translation\Loader;

use Zend\I18n\Translator;
use Zend\I18n\Translator\Loader;
use Zend\I18n\Translator\TextDomain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

use Zend\I18n\Translator\Loader\RemoteLoaderInterface;

class DatabaseTranslationLoader implements RemoteLoaderInterface
{
    protected $dbAdapter;
    protected $sm;

    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->dbAdapter = $this->sm->get('zf2translation_dbadapter');
    }

    /**
     * Load translations from a remote source.
     *
     * @param  string $locale
     * @param  string $textDomain
     * @return \Zend\I18n\Translator\TextDomain|null
     */
    public function load($locale, $messageDomain = 'database')
    {
        $textDomain = new TextDomain();
        $sql        = new Sql($this->dbAdapter);

        $messageDomain = 'database';

        $select = $sql->select();
        $select->from('messages');
        $select->columns(array(
                'message_key',
                'message_translation',
        ));
        $select->where(array(
                'locale_id'      => $locale,
                'message_domain' => $messageDomain,
                'message_plural_index' => null,
        ));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        $messages = $resultSet->initialize($stmt->execute());

        foreach ($messages as $message) {
            $textDomain[$message['message_key']] = $message['message_translation'];
        }

        return $textDomain;
    }
}
