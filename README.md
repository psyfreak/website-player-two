# PLAYER TWO V8 — References integrated

Complete Astro source assembled from V5 website + V7 character profile + V7.1 CSS fix + references component.

- Homepage: 8 logos between hero and quest board.
- About page: 26 logos in a dedicated section.
- Logo names and external URLs from the previous references patch.

**Important:** The source host could not be reached from the build environment, so the original logo binaries are **not included**. Until downloaded, the images load from entre.prenerds.com.

Run `python3 scripts/download-reference-logos.py` from a machine with access to the source site. When all downloads succeed, the script automatically switches the project to local logo files. No fabricated placeholders are used.

Run `npm install && npm run build` to verify in your own environment. This build was not run here unless separately confirmed.
