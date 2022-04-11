# Changelog

## [Unreleased]

### [2.0.0] - 2022-XX-XX

### Changed

- Use `contaoWidget` instead of `widget`for Contao settings

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

[Unreleased]: https://github.com/netzmacht/contao-form-bundle/compare/master..develop
[1.3.0]: https://github.com/netzmacht/contao-form-bundle/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/netzmacht/contao-form-bundle/compare/1.1.1...1.2.0
[1.1.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.2...1.1.0
[1.0.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/netzmacht/contao-form-bundle/compare/1.0.0...1.0.1
