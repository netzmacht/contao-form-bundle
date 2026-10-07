# Changelog

## [2.1.0] - Unreleased

### Breaking changes

 - Drop support for Contao 4.13, Contao `^5.3` is required
 - Drop support for `netzmacht/contao-toolkit` 3, `^4.0` is required
 - Drop support for Symfony 5.4, Symfony `^6.4 || ^7.4` is required
 - Remove the `google+` rgxp validation which is not available in Contao 5 anymore. Fields using
   `rgxp => 'google+'` are now passed to the `addCustomRegexp` hook
 - `DcaFormType` requires `Netzmacht\Contao\Toolkit\Dca\DcaManager` instead of the toolkit `Manager`

### Changed

 - The `Rgxp` constraint accepts named arguments, passing an options array is still supported
 - Use named arguments for the `Length` constraint

### Fixed

 - Pass all template variables required by Contao 5 to the rich text editor template so that TinyMCE gets initialized
 - Use the configured `minlength` instead of `maxlength` for the `minlength` attribute of form generator fields
 - The `Rgxp` constraint is validated again. It was assigned to no validation group, so the Contao rgxp rules were
   never checked. **Input which was accepted so far may now be rejected.**
 - `RgxpValidator` could not be created with Symfony 7 because it reset the validator context in its constructor
 - Use Contao's error messages for the `digit_*`, `friendly` and `fieldname` rgxp
 - Support values transformed by a form type (e.g. numbers of a `NumberType`, dates of a `DateType`) in the
   `RgxpValidator`

### Removed

 - Remove the obsolete `getExtendedType()` methods of the form type extensions, `getExtendedTypes()` is used instead

### [2.0.0] - 2022-08-17

### Changed

 - Use `contaoWidget` instead of `widget`for Contao settings
 - Introduce an own implementation of the fieldset type
 - Make all classes final

### [1.4.0] - 2022-01-18

### Changed

 - Bump dependencies for Contao and Symfony
 - Use `Symfony\Contracts\Translation\TranslatorInterface` insteadof `Symfony\Component\Translation\TranslatorInterface`
 - Use `Contao\CoreBundle\Framework\ContaoFramework` insteadof `Contao\CoreBundle\Framework\ContaoFrameworkInterface`

### [1.3.0] - 2020-11-24

### Added

 - Add experimental support for dca form mapping (checkbox, password, radio, select, textarea, text widgets)
 - Provide an `Rgxp` constraint based on symfony constraints
 - Recognize `rgxp` for form field model (Custom `rgxp` not supported)

### [1.2.1] - 2020-11-09

### Removed

 - Remove token storage alias but utilize crsrf token provider

### [1.2.0] - 2020-04-03

### Improvements

 - Auto detect if Contao request token should be added

### Added

 - Add new form type `ContaoRequestTokenType`
 
### Fixed

 - Support csrf token handling for Contao 4.4 and later on


## [1.1.1] - 2019-09-02

### Fixed

 - Remove dependency of contao.csrf.token_manager which isn't available in Contao 4.4
 - Force `clear:both` for button rows
 
## [1.1.0] - 2019-07-27

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
 - Add `contao_frontend` form theme
 
### Changed

 - Apply `tl_edit_form` and `tl_formbody_edit` wrapper in `form_start` and `form_end` blocks instead of in block `form`

## [1.0.2] - 2019-06-20

### Fixed

 - Fix form_themes setting. View template has to be a relative path to a defined 

## [1.0.1] - 2019-02-05 

## Added
 
 - Recognize size option for selects with multiple attribute

### Fixed

 - Do not include invisible form fields
 - Fix choices group for multiple select fields

[2.1.0]: https://github.com/netzmacht/contao-form-bundle/compare/2.0.3...master
[1.3.0]: https://github.com/netzmacht/contao-form-bundle/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/netzmacht/contao-form-bundle/compare/1.1.1...1.2.0
[1.1.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.2...1.1.0
[1.0.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.0...1.0.1
