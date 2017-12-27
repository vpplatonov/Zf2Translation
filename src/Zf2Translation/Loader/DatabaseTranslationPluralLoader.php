<?php

namespace Zf2Translation\Loader;

use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Sql;
use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\Translator\TextDomain;

use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

use Zend\ServiceManager\ServiceManager;

class DatabaseTranslationPluralLoader implements RemoteLoaderInterface
{
    protected $dbAdapter;

    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->dbAdapter = $this->sm->get('zf2translation_dbadapter');
    }

    /**
     * Load translations from a remote source.
     *
     * @param  string $locale
     * @param  string $filename
     * @return \Zend\I18n\Translator\TextDomain|null
     */
    public function load($locale, $messageDomain = 'database')
    {
        $textDomain = new TextDomain();
        $sql        = new Sql($this->dbAdapter);

        $select = $sql->select();
        $select->from('locales');
        $select->columns(array('locale_plural_forms'));
        $select->where(array('locale_id' => $locale));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($stmt->execute());

        $localeInformation = $result->current();

        if (!count($localeInformation)) {
            return $textDomain;
        }

        $textDomain->setPluralRule(
                PluralRule::fromString($localeInformation['locale_plural_forms'])
                );

        $select = $sql->select();
        $select->from('messages');
        $select->columns(array(
                'message_key',
                'message_translation',
                'message_plural_index'
        ));
        $select->where(array(
                'locale_id'      => $locale,
                'message_domain' => messageDomain,
                new \Zend\Db\Sql\Predicate\IsNotNull('message_plural_index'),
        ));

        $messages = $this->dbAdapter->query(
                $sql->getSqlStringForSqlObject($select),
                DbAdapter::QUERY_MODE_EXECUTE
                );

        foreach ($messages as $message) {
            if (isset($textDomain[$message['message_key']])) {
                if (!is_array($textDomain[$message['message_key']])) {
                    $textDomain[$message['message_key']] = array(
                            $message['message_plural_index'] => $textDomain[$message['message_key']]
                    );
                }

                $textDomain[$message['message_key']][$message['message_plural_index']] = $message['message_translation'];
            } else {
                $textDomain[$message['message_key']] = $message['message_translation'];
            }
        }

        return $textDomain;
    }

}
