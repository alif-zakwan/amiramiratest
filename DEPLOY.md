# Deploying ArifAmira to Render

Render doesn't have a native "PHP" runtime — you deploy PHP apps there with
Docker instead. The `Dockerfile` in this folder handles that (Apache +
PHP 8.2, with the `mbstring` extension the RSVP form needs). You don't need
to know Docker to use it — just follow the steps below.

## 1. Put this folder in a GitHub repo

Render deploys from a Git repository, so the project needs to live on
GitHub (or GitLab/Bitbucket) first.

1. Go to https://github.com/new and create a new repository (public or
   private both work) — e.g. `arifamira`.
2. Upload **everything in this zip** to that repo, keeping the folder
   structure as-is: the `Dockerfile` and `ArifAmira/` folder need to sit
   next to each other at the repo root. Easiest way if you're not
   comfortable with git commands: on the new repo's GitHub page, use
   "uploading an existing file" and drag the whole extracted folder in,
   or use GitHub Desktop.

## 2. Create the Web Service on Render

1. Sign up / log in at https://render.com (free tier is fine for this).
2. Click **New +** → **Web Service**.
3. Connect your GitHub account and select the repo you just created.
4. Render will ask for a few settings:
   - **Name**: anything, e.g. `arifamira` — this becomes part of your URL.
   - **Region**: pick whichever is closest to you (e.g. Singapore).
   - **Branch**: `main` (or whatever you pushed to).
   - **Runtime / Environment**: choose **Docker**. Render should
     auto-detect the `Dockerfile` at the repo root.
   - **Instance Type**: **Free** is enough to view it on your phone.
5. Click **Create Web Service**.

## 3. Wait for the build

Render will build the Docker image and deploy it — usually 1–3 minutes.
You'll see live logs. When it says the service is **Live**, you'll get a
URL like:

```
https://arifamira.onrender.com
```

Open that link on your phone (any browser, any network — it's a public
URL, not your local network) and you'll see the e-card exactly as it
looks locally.

To send someone a personalised link (fills in the "Kepada" name), use the
same `?to=` query the app already supports:

```
https://arifamira.onrender.com/?to=Puan+Salmah
```

## Things to know about the free tier

- **Cold starts**: a free Render service "spins down" after ~15 minutes
  of no traffic, and the next visit takes ~30–50 seconds to wake back up.
  Totally fine for testing/sharing casually; if you want it always
  instant for guests on the wedding day, upgrade the instance to a paid
  "Starter" plan before the event.
- **RSVP data isn't permanent on the free tier**: `data/rsvp.json` lives
  on the container's local disk, which is wiped on every redeploy and can
  reset when the service spins down/up. Fine for testing the RSVP flow,
  but before the real event you'll want one of:
  - Render's **persistent Disks** (paid, attaches a real volume you mount
    at `/var/www/html/data`), or
  - swapping the RSVP storage to a small managed database (Render offers
    free/low-cost PostgreSQL) if you want it bullet-proof.
  Happy to wire either of these up if you'd like.

## Updating the site later

Any time you edit files and push to the same GitHub branch, Render
automatically rebuilds and redeploys — no extra steps needed.
