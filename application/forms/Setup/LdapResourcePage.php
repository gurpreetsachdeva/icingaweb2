<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Form\Setup;

use Icinga\Web\Form;
use Icinga\Form\Config\Resource\LdapResourceForm;

/**
 * Wizard page to define the connection details for a LDAP resource
 */
class LdapResourcePage extends Form
{
    /**
     * Initialize this page
     */
    public function init()
    {
        $this->setName('setup_ldap_resource');
    }

    /**
     * @see Form::createElements()
     */
    public function createElements(array $formData)
    {
        $this->addElement(
            'hidden',
            'type',
            array(
                'required'  => true,
                'value'     => 'ldap'
            )
        );
        $this->addElement(
            'note',
            'description',
            array(
                'value' => t(
                    'Now please configure your AD/LDAP resource. This will later '
                    . 'be used to authenticate users logging in to Icinga Web 2.'
                )
            )
        );

        if (isset($formData['skip_validation']) && $formData['skip_validation']) {
            $this->addSkipValidationCheckbox();
        } else {
            $this->addElement(
                'hidden',
                'skip_validation',
                array(
                    'required'  => true,
                    'value'     => 0
                )
            );
        }

        $resourceForm = new LdapResourceForm();
        $this->addElements($resourceForm->createElements($formData)->getElements());
    }

    /**
     * Validate the given form data and check whether a BIND-request is successful
     *
     * @param   array   $data   The data to validate
     *
     * @return  bool
     */
    public function isValid($data)
    {
        if (false === parent::isValid($data)) {
            return false;
        }

        if (false === isset($data['skip_validation']) || $data['skip_validation'] == 0) {
            if (false === LdapResourceForm::isValidResource($this)) {
                $this->addSkipValidationCheckbox();
                return false;
            }
        }

        return true;
    }

    /**
     * Add a checkbox to the form by which the user can skip the connection validation
     */
    protected function addSkipValidationCheckbox()
    {
        $this->addElement(
            'checkbox',
            'skip_validation',
            array(
                'required'      => true,
                'label'         => t('Skip Validation'),
                'description'   => t('Check this to not to validate connectivity with the given directory service')
            )
        );
    }
}
