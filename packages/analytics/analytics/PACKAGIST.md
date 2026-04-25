# Publishing To Packagist

Package name:

```text
aetherpulse/prismpath
```

## 1. Create A GitHub Repository

Create a public repository named:

```text
prismpath
```

Recommended URL:

```text
https://github.com/aetherpulse/prismpath
```

If your GitHub username or organization is different, update these fields in `composer.json` first:

- `homepage`
- `support.issues`
- `support.source`

The Composer package name can stay `aetherpulse/prismpath` only if you want the Packagist vendor to be `aetherpulse`.

## 2. Push This Package Folder Only

Run these commands from this package directory:

```bash
cd packages/ultraclarity/analytics
git init
git add .
git commit -m "Initial PrismPath release"
git branch -M main
git remote add origin https://github.com/aetherpulse/prismpath.git
git push -u origin main
```

Do not push the full Laravel demo app. Packagist needs this package folder as the repository root because `composer.json` must be at the top level.

## 3. Tag The First Release

```bash
git tag v1.0.0
git push origin v1.0.0
```

## 4. Submit To Packagist

Open:

```text
https://packagist.org/packages/submit
```

Paste:

```text
https://github.com/aetherpulse/prismpath
```

Click **Check**, then **Submit**.

## 5. Install From Packagist

After Packagist indexes the repository:

```bash
composer require aetherpulse/prismpath
```

## 6. Future Releases

```bash
git add .
git commit -m "Describe the change"
git tag v1.0.1
git push origin main --tags
```

Packagist will update automatically when GitHub webhooks are enabled.

