Netzmacht Contao Form Bundle
============================

This bundle enables the power of Symfony forms in your Contao project.

The purpose if not (yet) to provide a fully replacement of Contao Core form handling but to enable you to use form 
configurations (form generator, data containers) in your application. 

Features
--------

 - Enables the symfony form component in your application
 - Provides a FormGeneratorType to use form based on the backend
 
### Form generator

If you want to use the form generator to configure your symfony forms - you can do it. This extension
ships a FromGeneratorType.

**Usage**

```php
// Symfony form generator, provided as service form.factory
$form = $formFactory->create(Netzmacht\ContaoFormBundle\Form\FormGeneratorType::class, null, ['formId' => 5]);

// That's all. Now you can use a symfon form having your form generator form fields.

```

### Backend form theme

This bundle also provides a form theme for the Contao backend. You can enable it in your twig template where the form is
used:

```twig`
{% form_theme form '@NetzmachtContaoForm/form/contao_backend.html.twig' %}
{{ form(form) }}
``

Roadmap
-------

 - Provide a form theme for Contao backend and frontend standards.
 - Provide DcaType for data container based forms
 - Implement input sanitizing as an optional feature
 - Support popular 3rd party form fields
 
Known limitations
-----------------
  
 - Missing input sanitizing. At the moment Contao input sanitizing is bypassed. If you have to use it
   you have to manually sanitize the data.
 - Unsupported form fields are just ignored (form generator) 
 - Submit buttons with images are not supported.
 - Upload settings to handle the file upload is not recognized.
