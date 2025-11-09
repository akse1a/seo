# Package Publishing Guide

## Step 1: Initialize Git Repository

```bash
git init
git add .
git commit -m "Initial commit"
```

## Step 2: Create GitHub Repository

1. Create a new repository on GitHub named `seo`
2. Add remote:

```bash
git remote add origin https://github.com/akse1a/seo.git
git branch -M main
git push -u origin main
```

## Step 3: Create Version Tag

### Automatically (Recommended)

```bash
# Use release.sh script
./release.sh 1.0.0
```

### Manually

```bash
# Create tag
git tag -a v1.0.0 -m "Version 1.0.0"

# Push tag to GitHub
git push origin v1.0.0

# Or push all tags
git push --tags
```

For more information about versioning, see [VERSIONING.md](VERSIONING.md)

## Step 4: Publish to Packagist

1. Register on [packagist.org](https://packagist.org)
2. Click "Submit" and enter your repository URL: `https://github.com/akse1a/seo`
3. Packagist will automatically detect your `composer.json` and publish the package

## Step 5: Configure Auto-update (Optional)

1. In your GitHub repository settings, go to "Settings" → "Webhooks"
2. Add a webhook with URL: `https://packagist.org/api/github?username=YOUR_PACKAGIST_USERNAME`
3. In Packagist, go to package settings and enable "Auto-update"

After this, the package will be available for installation via:

```bash
composer require akse1a/seo
```
