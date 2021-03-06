Kirjastot.fi Form Bundle
========================

Drupal 8 module that contains useful general-purpose form fields.

P.S. Please do not put any ad hoc hacks here.

## Bundle content

| Element | Identifier
| ------- | ----------
| Captcha | kifiform_captcha
| View counter | kifiform_view_counter

### Captcha
Captcha is a simple user verification tool. It presents a textual question and expects a valid answer.

### View counter
Counts displays of given entity. Counter is implemented as an AJAX call so it works even when pages
are being served from cache. It is implemented as a form field so it is also possible to use multiple
counters per entity for different purposes.

## Theming
Provided stylesheets are written in Sass using SCSS syntax. They can be compiled with Compass.
Configuration for the app is provided in [config.rb](public/config.rb).

```
cd public/
compass watch .
```
