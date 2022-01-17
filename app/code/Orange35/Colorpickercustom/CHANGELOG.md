#CHANGELOG

## [2.0.3] - 2019-09-25
- FIX: cast result of Option:getValues() to an array to avoid warnings

## [2.0.2] - 2019-05-27
- fix upgrade script issue in database with table prefix

## [2.0.1] - 2019-03-09
- magento 2.2.8 compatibility fix 

## [2.0.0] - 2019-03-14
- color swatches on category and search result pages
- new slider configuration and behavior
- support Magento 2.3.x

## [1.3.1] - 2019-01-23
- Support text swatches if there is neither image nor color
- fix dependency with Orange35_ColorPickerElement

## [1.3.0] - 2019-01-09
- Support 'Entity Type = Products' import & export
- Addited configuration fields: Selected Swatch Outline and Selected Swatch Outline Color

## [1.2.1] - 2018-10-24
- Added Show Price config option
- Don't show zero price like $0.000 
 
## [1.2.0]
- Added Choice Method config option (Toggle or Ctrl+Click to change selected swatches).

## [1.1.1]
- Less conflicts with custom theme (MAGE-842)

## [1.1.0] - 2018-04-03
- Product Edit Form. Show/hide fields depended on a Colorpicker checkbox.

## [1.0.2] - 2017-09-11

###Product Detail Page
- Use hidden select in case of Option Type = (Drop-down, Radio Buttons, Checkbox,  Multiselect). 
- Use native magento behaviour to calculate product price based on selected custom options.
- Added 'field' and 'required' classes (if needed) to custom option to be compatible with other 3rd party modules like Pektsekye_OptionConfigurable v1.0.0. 

###Product Edit Page
- Fixed issue with removing image when magento option values saving strategy is changed from 'delete all+insert' to 'update' like in Pektsekye_OptionConfigurable v1.0.0.
- Set is_delete column to the last position.
