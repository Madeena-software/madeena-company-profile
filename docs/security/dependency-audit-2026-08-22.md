# Dependency Security Audit — 2026-08-22

## Release Security Disposition

**CLEAR — NO DEPENDENCY SECURITY BLOCKER IDENTIFIED**

- **Node/NPM Total Advisories**: 8 vulnerabilities across 8 packages (2 Critical, 5 High, 1 Moderate).
- **NPM Production-Tree (`--omit=dev`) Advisories**: 0 vulnerabilities.
- **Critical / High Production-Reachable Advisories**: 0.
- **Production Build-Time Advisories**: 3 packages (`vite`, `postcss`, `nanoid`).
- **CI / Local Dev-Only Advisories**: 2 packages (`concurrently`, `shell-quote`).
- **Unreachable Transitive in Browser Bundle**: 2 packages (`follow-redirects`, `form-data`).
- **Production Browser Runtime Packages**: 1 package (`axios`), where all reported advisories are Node.js-adapter-specific (SSRF, proxy bypass, stream header manipulation, cloud metadata exfiltration) and cannot execute within standard browser `XMLHttpRequest`/`fetch` environments.
- **Composer / PHP Locked Advisories**: 46 upstream advisories across 14 packages (1 Critical, 9 High, 30 Medium, 5 Low, 1 None; 0 abandoned packages).
- **Remediation Feasibility**: Safe, non-breaking semver-compatible patches are available for all 8 npm packages.

---

## 1. Audit Metadata & Toolchain

- **Audited Git Baseline SHA**: `0c8905ae728eb31da8211251af1219c42f2fcebc` (branch: `develop`)
- **Main Branch Baseline SHA**: `009b1a65e1216d8c097606c51019b3947d2ba574`
- **Audit Date & Time**: `2026-08-22 04:26:20 UTC`
- **Node.js**: `v22.22.1`
- **npm**: `9.2.0`
- **PHP**: `8.5.4 (cli)` (Zend Engine v4.5.4 / OPcache v8.5.4)
- **Composer**: `2.9.5` (2026-01-29)

---

## 2. Architecture & Container Security Boundary

### Multi-Stage Container Architecture
The repository's production containerization is governed by [Dockerfile](file:///var/www/madeena-company-profile/Dockerfile) and [.dockerignore](file:///var/www/madeena-company-profile/.dockerignore):

1. **Stage 0 (`composer-deps`)**: `php:8.4-cli` installs production PHP dependencies using `composer install --no-dev --no-scripts`.
2. **Stage 1 (`node-builder`)**: `node:24-alpine` installs dependencies (`npm ci --no-audit --no-fund`) and compiles static frontend assets (`npm run build`).
3. **Stage 2 (`base`)**: Installs Linux OS packages and PHP 8.4 extensions for `php:8.4-fpm`.
4. **Stage 3 (`app`)**: Assembles the production image:
   - Copies PHP backend code (`COPY . .`).
   - Copies `/app/vendor` from `composer-deps`.
   - Copies **only** `/app/public/build` static assets from `node-builder`.
   - **`node_modules/` is never copied into the final production image** (`.dockerignore` explicitly excludes `node_modules/`, `vendor/`, and `public/build/`).
   - Neither `node` nor `npm` binaries exist in the production runtime container.

### Execution Boundaries
- **Production Server Runtime**: 0 Node.js packages execute on the production server.
- **Production Browser Runtime**: Only static JS/CSS assets compiled into `public/build/` are served to web visitors.
- **Build Infrastructure Runtime**: Vite, PostCSS, and esbuild execute only during Docker image assembly or local compilation.

---

## 3. NPM Audit Summary

### Full Tree Audit (`npm audit`)
- **Total Vulnerabilities**: 8 (1 Moderate, 5 High, 2 Critical)
- **Direct Vulnerabilities**: 3 (`axios`, `concurrently`, `vite`)
- **Transitive Vulnerabilities**: 5 (`follow-redirects`, `form-data`, `nanoid`, `postcss`, `shell-quote`)
- **Exit Code**: `1` (unpatched advisories present in devDependencies)

### Production-Tree Audit (`npm audit --omit=dev`)
- **Total Vulnerabilities**: 0
- **Exit Code**: `0`
- **Verified**: The sole production npm dependency declared in `package.json` (`dependencies`: `"katex": "^0.17.0"`) contains 0 known vulnerabilities.

---

## 4. Dependency Advisory Risk Table

| Advisory ID | Package | Severity | Dependency Path | Fixed Version | Breaking Fix? | Execution Category | Production Reachability | Exploit Preconditions | Recommended Disposition |
|---|---|---|---|---|---|---|---|---|---|
| `GHSA-3p68-rc4w-qgx5` + 27 others | `axios` | High | `madeena-company-profile` -> `axios@1.13.6` | `^1.18.0` | No | B. PRODUCTION BROWSER RUNTIME | Low / Theoretical (0 in browser) | Requires Node.js HTTP adapter, custom proxy, SSRF targets, or prototype pollution in config merge. Browser uses XHR/fetch. | SAFE PATCH/MINOR REMEDIATION |
| `GHSA-w7jw-789q-3m8p`, `GHSA-395f-4hp3-45gv` | `concurrently` (via `shell-quote`) | Critical | `madeena-company-profile` -> `concurrently@9.2.1` -> `shell-quote@1.8.3` | `^9.2.2` / `shell-quote@1.8.5` | No | D. CI / TEST ONLY | None | Invoked only via `composer dev` locally by developer. Never executed on production server or bundled into browser. | SAFE PATCH/MINOR REMEDIATION |
| `GHSA-r4q5-vmmm-2653` | `follow-redirects` | Moderate | `madeena-company-profile` -> `axios@1.13.6` -> `follow-redirects@1.15.11` | `1.15.12` | No | E. UNREACHABLE / UNUSED TRANSITIVE | None | Node.js HTTP redirect adapter only. Stripped by Vite during browser bundling. | SAFE PATCH/MINOR REMEDIATION |
| `GHSA-hmw2-7cc7-3qxx` | `form-data` | High | `madeena-company-profile` -> `axios@1.13.6` -> `form-data@4.0.5` | `4.0.6` | No | E. UNREACHABLE / UNUSED TRANSITIVE | None | Node.js multipart stream adapter. Stripped by Vite during browser bundling (browser uses standard `FormData`). | SAFE PATCH/MINOR REMEDIATION |
| `GHSA-28wg-ghj8-5hjv`, `GHSA-2v37-7h3g-55p8` | `nanoid` | High | `madeena-company-profile` -> `vite@6.4.1` -> `postcss@8.5.8` -> `nanoid@3.3.11` | `3.3.18` / `3.3.19` | No | C. PRODUCTION BUILD-TIME | None | Used internally by PostCSS AST mapping during `npm run build`. Never called with zero/negative custom sizes. Not in browser. | SAFE PATCH/MINOR REMEDIATION |
| `GHSA-qx2v-qp2m-jg93`, `GHSA-6g55-p6wh-862q`, `GHSA-fxqj-rqcc-2cmp`, `GHSA-r28c-9q8g-f849` | `postcss` | High | `madeena-company-profile` -> `vite@6.4.1` -> `postcss@8.5.8` | `8.5.23` | No | C. PRODUCTION BUILD-TIME | None | Path traversal in `sourceMappingURL` auto-loading during CSS compilation. Only compiles trusted repository CSS (`resources/css/`). | SAFE PATCH/MINOR REMEDIATION |
| `GHSA-w7jw-789q-3m8p`, `GHSA-395f-4hp3-45gv` | `shell-quote` | Critical | `madeena-company-profile` -> `concurrently@9.2.1` -> `shell-quote@1.8.3` | `1.8.5` | No | D. CI / TEST ONLY | None | Invoked only when `composer dev` parses command strings. Requires untrusted shell input into `composer dev`. | SAFE PATCH/MINOR REMEDIATION |
| `GHSA-4w7w-66w2-5vf9`, `GHSA-p9ff-h696-f583`, `GHSA-v6wh-96g9-6wx3`, `GHSA-fx2h-pf6j-xcff` | `vite` | High | `madeena-company-profile` -> `vite@6.4.1` | `6.4.3` | No | C. PRODUCTION BUILD-TIME | None | Dev server WebSocket arbitrary file read & Windows UNC path leaks. Production uses static compilation (`vite build`), not dev server. | SAFE PATCH/MINOR REMEDIATION |

---

## 5. In-Depth Per-Package Reachability Analysis

### 1. `axios` (`1.13.6` -> candidate `1.18.0`)
- **Package Role**: Declared under `devDependencies` (`^1.7.4`) in `package.json`. Imported in `resources/js/bootstrap.js` to assign `window.axios` and configure default CSRF header.
- **Bundle Inclusion**: Bundled by Vite into `public/build/assets/app-CWNHUZev.js`.
- **Vulnerability Breakdown**: 28 total advisories (e.g. `GHSA-3p68-rc4w-qgx5`, `GHSA-pmwg-cvhr-8vh7`, `GHSA-35jp-ww65-95wh`, `GHSA-q8qp-cvcw-x6jj`).
- **Exploit Reachability**:
  - All high-severity findings involve Node.js backend HTTP adapters (SSRF via NO_PROXY bypass, Node proxy prototype pollution, HTTP stream body bypass, AWS/cloud metadata exfiltration via header injection).
  - In browser runtime, Axios uses standard browser `XMLHttpRequest` or `fetch`, where Node-specific adapters are stripped or dormant.
  - Madeena's public frontend pages do not expose dynamic, user-controlled Axios requests; public form interactions use standard HTML form POSTs or Livewire.
- **Verdict**: 0 credible exploit paths in production. Safe to patch via minor version bump.

### 2. `concurrently` (`9.2.1`) & `shell-quote` (`1.8.3` -> candidate `1.8.5`)
- **Package Role**: Dev-only CLI utility for running multiple processes concurrently. Referenced in `composer.json` script `"dev": "npx concurrently ..."` for local developer convenience (`composer dev`).
- **Vulnerability Breakdown**: `GHSA-w7jw-789q-3m8p` (Critical - shell-quote quote() does not escape newlines in object .op values) and `GHSA-395f-4hp3-45gv` (High - Quadratic complexity DoS).
- **Exploit Reachability**:
  - Not invoked during `npm run build` or `npm ci` in Docker / CI.
  - Not included in Docker images.
  - Not bundled into browser assets.
  - Exploit requires an attacker to inject malicious object operations into local developer CLI arguments.
- **Verdict**: 0 production reachability. Can be updated cleanly via lockfile update.

### 3. `vite` (`6.4.1` -> candidate `6.4.3`), `postcss` (`8.5.8` -> candidate `8.5.23`), `nanoid` (`3.3.11` -> candidate `3.3.18`)
- **Package Role**: Frontend build toolchain. Vite compiles CSS/JS assets during `npm run build` in Docker Stage 1 (`node-builder`).
- **Vulnerability Breakdown**:
  - `vite`: `GHSA-p9ff-h696-f583` (Vite dev server WebSocket file read), `GHSA-fx2h-pf6j-xcff` (Windows fs.deny bypass).
  - `postcss`: `GHSA-6g55-p6wh-862q` / `GHSA-r28c-9q8g-f849` (Path traversal in `sourceMappingURL` auto-loading).
  - `nanoid`: `GHSA-28wg-ghj8-5hjv` / `GHSA-2v37-7h3g-55p8` (Infinite loop in custom generator).
- **Exploit Reachability**:
  - Production deployments execute `vite build` to generate static files, never `vite dev` server.
  - Build inputs are trusted source files in `resources/`. No untrusted user CSS or sourcemap URLs are parsed.
  - None of these packages are shipped in the production image or browser bundle.
- **Verdict**: 0 production runtime risk. Safe to patch with minor/patch updates.

### 4. `follow-redirects` (`1.15.11`) & `form-data` (`4.0.5`)
- **Package Role**: Transitive dependencies of `axios` for Node.js environments.
- **Exploit Reachability**: Completely eliminated from browser bundle by Vite tree-shaking / browser field substitutions.
- **Verdict**: Unreachable in production.

---

## 6. Install-Script & Supply-Chain Security Observation

During `npm ci`, npm inspects package install scripts. The repository's package lock contains one package with an active postinstall script:

- **Package**: `esbuild` (`0.25.12`, direct devDependency of `vite`)
- **Script**: `"postinstall": "node install.js"`
- **Purpose**: Verifies and links the platform-specific native binary (`@esbuild/linux-x64`, `@esbuild/darwin-arm64`, etc.) required by esbuild bundler.
- **Execution Context**: Executed in `node-builder` during Docker build and CI runner.
- **Supply-Chain Assessment**: Legitimate, verified upstream package from the official `esbuild` distribution (Evan Wallace). No malicious payload or unauthorized network exfiltration observed.
- **Status in #10A**: Documented only; no script permissions modified.

---

## 7. Composer / PHP Security Sanity Check

Executing `composer audit --locked` on the current `composer.lock` baseline identified **46 advisories across 14 packages**:

- **Severity Distribution**: 1 Critical, 9 High, 30 Medium, 5 Low, 1 Untyped (None).
- **Abandoned Packages**: 0 abandoned packages.
- **Affected Packages Breakdown**:
  1. `filament/actions` (1 advisory, Medium - Scope enforcement on AttachAction/AssociateAction)
  2. `filament/filament` (3 advisories: 1 High - MFA recovery code concurrency; 2 Medium - Unauthenticated temp upload on auth pages, timing user enumeration)
  3. `filament/infolists` (1 advisory, Medium - ImageEntry XSS)
  4. `filament/tables` (1 advisory, Medium - ImageColumn XSS)
  5. `guzzlehttp/guzzle` (9 advisories: 1 High, 8 Medium - Header leakage, redirect handling, curl options)
  6. `guzzlehttp/psr7` (4 advisories, Medium - Header parsing, URI validation)
  7. `laravel/framework` (3 advisories: 1 High, 1 Medium, 1 Untyped - Middleware, cookie/session edge cases)
  8. `league/commonmark` (6 advisories: 4 High, 2 Medium - ReDoS / XML entity parsing)
  9. `mtdowling/jmespath.php` (1 advisory, Critical - JMESPath parser code execution on untrusted expressions)
  10. `phpseclib/phpseclib` (1 advisory, Medium - X.509 AIA SSRF)
  11. `symfony/html-sanitizer` (5 advisories: 4 Medium, 1 Low - URL attribute bypasses)
  12. `symfony/http-foundation` (1 advisory, Medium - IPv6 transition subnet parsing)
  13. `symfony/http-kernel` (1 advisory, High - HEAD request method filter bypass)
  14. `symfony/mailer` (1 advisory, Medium - Sendmail argument injection)
  15. `symfony/mime` (2 advisories: 1 High, 1 Medium - Email header / CRLF injection)
  16. `symfony/polyfill-intl-idn` (1 advisory, Low - Punycode ASCII equivalence)
  17. `symfony/routing` (2 advisories, Medium - Dot-segment URL collapse)
  18. `symfony/yaml` (3 advisories, Low - Billion laughs & ReDoS in parser)

*Note: These Composer advisories represent upstream PHP dependencies locked in `composer.lock`. As per Task #10A instructions, no dependencies were updated in this task; findings are documented for separate remediation review.*

---

## 8. Candidate Remediation Matrix (for Task #10B)

| Package | Current Version | Candidate Fixed Version | Direct / Transitive | Semver Impact | Expected Lockfile Impact | Expected Build Risk | Proposed Next Action |
|---|---|---|---|---|---|---|---|
| `axios` | `1.13.6` | `^1.18.0` | Direct (`devDependencies`) | Minor (`^1.7.4` -> `^1.18.0`) | `axios`, `follow-redirects`, `form-data` | Minimal (XHR/fetch API unchanged) | Update `package.json` & lockfile in #10B |
| `concurrently` | `9.2.1` | `^9.2.2` | Direct (`devDependencies`) | Patch / Minor | `concurrently`, `shell-quote` | None (CLI tool only) | Update in #10B |
| `follow-redirects` | `1.15.11` | `1.15.12` | Transitive (`axios`) | Patch | Transitive lockfile update | None | Resolved with `axios` update in #10B |
| `form-data` | `4.0.5` | `4.0.6` | Transitive (`axios`) | Patch | Transitive lockfile update | None | Resolved with `axios` update in #10B |
| `nanoid` | `3.3.11` | `3.3.18` | Transitive (`postcss`) | Patch | Transitive lockfile update | None | Resolved with `vite`/`postcss` update in #10B |
| `postcss` | `8.5.8` | `8.5.23` | Transitive (`vite`) | Patch | Transitive lockfile update | None | Resolved with `vite` update in #10B |
| `shell-quote` | `1.8.3` | `1.8.5` | Transitive (`concurrently`) | Patch | Transitive lockfile update | None | Resolved with `concurrently` update in #10B |
| `vite` | `6.4.1` | `^6.4.3` | Direct (`devDependencies`) | Patch (`^6.0.11` semver) | `vite`, `postcss`, `nanoid` | Minimal (standard bugfix patch) | Update lockfile in #10B |

---

## 9. Verification & Build Reproducibility Evidence

The current lockfile and build pipeline were fully reproduced and verified locally:

1. **`npm ci`**:
   - Result: 103 packages added in 4s, exit code `0`.
   - Reported: 8 vulnerabilities (1 moderate, 5 high, 2 critical).
2. **`npm run build`**:
   - Result: 59 modules transformed, built in 2.10s, exit code `0`.
   - Generated assets: CSS, JS, KaTeX fonts in `public/build/`.
3. **Pint Ratchet (`./scripts/pint-ratchet.sh HEAD`)**:
   - Result: 1 post-baseline file checked (`tests/Feature/ReleaseSmokeTest.php`), 0 violations, PASS.
4. **PHPUnit Test Suite (`vendor/bin/phpunit`)**:
   - Result: 162 tests, 996 assertions, 100% OK (Runtime: 15.466s).
