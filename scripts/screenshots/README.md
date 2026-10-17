# Documentation screenshots

Takes the screenshots of `docs/images` in English, French and German with Playwright.

## Prerequisites

* Deming running with test data and an administrator account
* Node dependencies installed (`npm install`)
* A Chromium browser: `npx playwright install chromium`, or an existing one set with `CHROME_PATH`

## Usage

```bash
npm run docs:screenshots
```

Examples:

```bash
# Use an installed Chrome and another account
CHROME_PATH=/usr/bin/google-chrome DEMING_LOGIN=admin DEMING_PASSWORD=secret npm run docs:screenshots

# Only some screens, in French
LANGS=fr ONLY=home1,r0,risk.matrix npm run docs:screenshots

# The creation screens in the three languages
LANGS=en,fr,de ONLY=m4,c4,a3,a4,ex3,ex4 npm run docs:screenshots

# Use a given control and measurement instead of the first ones of the lists
ID_control=18 ID_measure_done=1231 npm run docs:screenshots
```

| Variable          | Default                  | Description                                   |
|-------------------|--------------------------|-----------------------------------------------|
| `DEMING_URL`      | `http://localhost:8000`  | Application URL                               |
| `DEMING_LOGIN`    | `admin@admin.localhost`  | Administrator login                           |
| `DEMING_PASSWORD` | `admin`                  | Administrator password                        |
| `CHROME_PATH`     | Playwright's Chromium    | Browser executable                            |
| `LANGS`           | `en,fr`                  | Languages to capture (`en`, `fr`, `de`)       |
| `ONLY`            | all screens              | Screen names to capture (without `.{fr}`)     |
| `OUT_DIR`         | `docs/images`            | Output directory                              |
| `ID_<lookup>`     |                          | Fixed value for a lookup of `screens.json`    |

## screens.json

* `lookups`: values used in the screen URLs (`{control}`, `{risk}`...). Each one is read from the first element
  matching `selector` on `page` (attribute `attr`, `href` by default, filtered by `regex`).
* `screens`: one entry per image.
    * `name`: image name; `{fr}` is replaced by the language in French and German (`fr`, `de`) and removed in English, `{lang}` is replaced by the language
    * `url`: page to capture
    * `selector`: element to capture (default: the main content)
    * `until`: stops the capture at the top of this element
    * `maxHeight`: maximum height in pixels
    * `wait`: delay in ms before the capture (default: 1200)

The language is selected with the `change_language` parameter, so the user's language is not modified.
The lookups set the filters of the measurement list in the session of the account used.
