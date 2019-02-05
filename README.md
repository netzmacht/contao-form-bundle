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

```twig
{% form_theme form '@NetzmachtContaoForm/form/contao_backend.html.twig' %}
{{ form(form) }}
```

### Input filtering

Be aware that the **Contao input sanitizing is bypassed by default**. If you need the data filtered, especially when 
using it in legacy context (f.e. Contao templates) you can filter the data by using the provided input filter
`Netzmacht\ContaoFormBundle\Filter\ContaoInputFilter` which is provided as service `netzmacht.contao_form.input_filter`.

### Upload handler

By default the uploaded file is available as `UploadedFile` instance in the form data. If you want to apply the 
configured setting form the form field, you might use the `Netzmacht\ContaoFormBundle\Form\FormGenerator\UploadHandler` 
class provided as service `netzmacht.contao_form.form_generator.upload_handler`.

Roadmap
-------

 - Provide a form theme for Contao backend and frontend standards.
 - Provide DcaType for data container based forms
 - Support popular 3rd party form fields
 
Known limitations
-----------------

 - Unsupported form fields are just ignored (form generator) 
 - Submit buttons with images are not supported.
