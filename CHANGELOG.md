
# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 3.1.0 - TBD

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.4...develop)

### Added

 - Custom route to load map data.
 - Add distance filter support for the marker layer (Bounds Mode "fit" has to be enabled).
 - Add support for relative css units for map size definition (#59).

### Deprecated

 - Deprecate `Netzmacht\Contao\Leaflet\Frontend\RequestUrl`. Use router to generate request url for layer data.
 - Deprecate `Netzmacht\Contao\Leaflet\Frontend\DataController`. Use introduced endpoint to get map data.
 
### Changed

 - Rewritten about page using own route (#48).

### Fixed

 - Pressing enter on backend geocode control doesn't submit form anymore.
 - Fix broken marker cluster layer (#60).


## 3.0.4 - 2018-10-08

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.3...3.0.4)

### Fixed

 - Fix broken content element attributes (Missing class and custom id).

## 3.0.3 - 2018-09-18

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.2...3.0.3)

### Fixed

 - Make alias generator services public for Contao 4.6/Symfony 4.0 compatibility.

## 3.0.2 - 2018-08-23

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.1...3.0.2)

 - Run composer require checker and solve issues.
 
## 3.0.1 - 2018-06-20

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.0...3.0.1)

 - Fix broken dynamic bbox related data loading (#57) 

## 3.0.0 - 2018-01-05

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.0-beta1...3.0.0)

 - Make hook/dca listener services public

## 3.0.0-beta1 - 2017-11-15

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.0-alpha2...3.0.0-beta1)

Enhancements

  - Updated translations

Bugfixes
  
  - Broken service definitions
  - Broken file layer id 

## 3.0.0-alpha1 - 2017-10-19

[Full Changelog](https://github.com/netzmacht/contao-leaflet-maps/compare/3.0.0-alpha1...3.0.0-alpha2)

Implemented enhancements
 
 - Refactor to a more service oriented architecture
 - Use a proper template for the map templates (Customize templates has to be adjusted!)
 - New file layer for gpx,kml,wkt,topojson,geojson files added
 - Bypass filesystem cache in debug mode
 - Changelog added
