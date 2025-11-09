# Library Versioning

## How to Set Version

In Composer, package version is determined through **git tags**, not through the `version` field in `composer.json`.

## Versioning System (Semantic Versioning)

Format used: `MAJOR.MINOR.PATCH`

- **MAJOR** (1.0.0) - incompatible API changes
- **MINOR** (0.1.0) - new functionality with backward compatibility
- **PATCH** (0.0.1) - bug fixes with backward compatibility

## Creating a Version

### Step 1: Ensure All Changes Are Committed

```bash
git status
git add .
git commit -m "Description of changes"
```

### Step 2: Create Git Tag

```bash
# For version 1.0.0
git tag -a v1.0.0 -m "Version 1.0.0"

# Or for other versions
git tag -a v1.0.1 -m "Version 1.0.1 - Bug fixes"
git tag -a v1.1.0 -m "Version 1.1.0 - New features"
git tag -a v2.0.0 -m "Version 2.0.0 - Breaking changes"
```

### Step 3: Push Tag to GitHub

```bash
# Push single tag
git push origin v1.0.0

# Or push all tags
git push --tags
```

### Step 4: Packagist Will Automatically Detect New Version

After creating a tag on GitHub, Packagist will automatically detect the new version (if auto-update is configured).

## Viewing All Versions

```bash
# List all tags
git tag -l

# Detailed information about tag
git show v1.0.0
```

## Deleting a Version (If Needed)

```bash
# Delete tag locally
git tag -d v1.0.0

# Delete tag on GitHub
git push origin --delete v1.0.0
```

## Recommendations

1. **Always use `v` prefix** for tags (v1.0.0, not 1.0.0)
2. **Write meaningful messages** in tag messages
3. **Don't delete published versions** without extreme necessity
4. **Follow Semantic Versioning** for clarity of changes

## Version Examples

```
v0.1.0  - First working version (alpha/beta)
v0.2.0  - New features added
v0.2.1  - Bug fixes
v1.0.0  - Stable release
v1.0.1  - Fixes in stable version
v1.1.0  - New features (backward compatible)
v2.0.0  - Critical API changes
```

## Automation

For automatic version creation, you can use a script:

```bash
#!/bin/bash
VERSION=$1
if [ -z "$VERSION" ]; then
    echo "Usage: ./release.sh 1.0.0"
    exit 1
fi

git tag -a "v$VERSION" -m "Version $VERSION"
git push origin "v$VERSION"
echo "Version v$VERSION created and pushed"
```
