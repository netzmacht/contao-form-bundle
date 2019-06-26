# Changelog

## [Unreleased]

### Added

 - Render mandatory hint in the `contao_backend` theme
 - Add `toggleable` option for fieldsets
 - Backport `help` message option from Symfony 4.1
 - Add `rte` option for `TextAreaType` to support RTE in the Contao backend 
 - Add widget option for every form type allowing to define `class`, `fe_class`, `be_class` css attributes
 - Add class `Netzmacht\ContaoFormBundle\Form\FormGenerator\UploadHandler` and service 
   `netzmacht.contao_form.form_generator.upload_handler` to handle form uploads
 - Add File constraint for uploaded files recognizing supported extensions and max size settings
 - Added `contao_request_token()` function for twig
 - Start `contao_frontend` form theme: Containing REQUEST_TOKEN 

## [1.0.2] - 2019-06-20

### Fixed

 - Fix form_themes setting. View template has to be a relative path to a defined 

## [1.0.1] - 2019-02-05 

## Added
 
 - Recognize size option for selects with multiple attribute

### Fixed

 - Do not include invisible form fields
 - Fix choices group for multiple select fields

[Unreleased]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.2...dev-develop
[1.0.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.0...1.0.1
