<?php

namespace Fuz\AppBundle\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Form\Form;

class BaseController extends Controller
{

    /**
     * Symfony's var_dump
     *
     * @param mixed $var
     */
    protected function dump($var)
    {
        VarDumper::dump($var);
    }

    /**
     * This method comes from Flip's answer on Stackoverflow:
     * http://stackoverflow.com/a/17428869/731138
     *
     * @param Form $form
     * @return array
     */
    protected function getErrorMessages(Form $form)
    {
        $errors = array ();

        foreach ($form->getErrors() as $error)
        {
            if ($form->isRoot())
            {
                $errors['#'][] = $error->getMessage();
            }
            else
            {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child)
        {
            if (!$child->isValid())
            {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    /**
     * Builds a javascript-friendly list of errors for validating forms using ajax.
     *
     * Implementation example:
     *
     * In your twig views, use for each of your fields:
     *
     * <div id="errors-{{ form.field.vars.id }}">{{ form_errors(form.field) }}</div>
     *
     * In jQuery, you'll use something like that after your ajax call:
     *
     * $.each(data['errors'], function(id, errors) {
     *    var html = '<div class="errors">';
     *    $.each(errors, function(index, error) {
     *       html += '<p class="error">' + error + '</p>';
     *    }
     *    html += '</div>';
     *    if (id !== '#') {
     *      $("#errors-" + id).html(html);
     *    } else {
     *      $("#general-errors").html(html);
     *    }
     * });
     *
     * Note: ids are built following FormDataExtractor::buildId's method pattern.
     *
     * @param Form $form
     * @return array
     */
    protected function getErrorMessagesAjaxFormat(Form $form)
    {
        $originalErrors = $this->getErrorMessages($form);

        $globalErrors = null;
        if (array_key_exists('#', $originalErrors))
        {
            $globalErrors = $originalErrors['#'];
            unset($form['#']);
        }

        $normalizedErrors = $this->normalizeErrorMessagesAjaxFormat($originalErrors, $form->getName());
        if (!is_null($globalErrors))
        {
            $normalizedErrors['#'] = $globalErrors;
        }

        $this->dump($normalizedErrors);
        return $normalizedErrors;
    }

    private function normalizeErrorMessagesAjaxFormat(array $errors, $prefix)
    {
        $normalizedErrors = array ();
        foreach ($errors as $key => $error)
        {
            if (is_array($error))
            {
                $normalizedErrors = array_merge($normalizedErrors,
                   $this->normalizeErrorMessagesAjaxFormat($error, "{$prefix}_{$key}"));
            }
            else
            {
                $normalizedErrors[$prefix][$key] = $error;
            }
        }
        return $normalizedErrors;
    }

}
