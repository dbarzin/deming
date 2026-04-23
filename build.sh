#!/usr/bin/env bash
set -euo pipefail

# -------- Configuration --------
# Par défaut, utilise la date locale de la machine. Tu peux forcer le fuseau :
# export TZ="Europe/Luxembourg"
TAG="$(date +%Y.%m.%d)"            # format yyyy.mm.dd
REPO="dbarzin/deming"              # ex: "owner/mon-repo" ; si vide, déduit depuis 'git'
BRANCH="main"                      # branche de référence
TITLE="${TITLE:-Release $TAG}"     # titre de la release
NOTES_MODE="auto"                  # auto|changelog|file
CHANGELOG_FILE="${CHANGELOG_FILE:-}"  # utilisé si NOTES_MODE=file
ASSETS=(${ASSETS:-})               # ex: ASSETS="dist/app.tar.gz build/report.txt"
DRAFT="false"                      # true|false
PRERELEASE="false"                 # true|false
PUSH_TAG="true"                    # true|false

# -------- Fonctions utilitaires --------
fail() { echo "Erreur: $*" >&2; exit 1; }

need_cmd() { command -v "$1" >/dev/null 2>&1 || fail "Commande requise manquante: $1"; }

deduce_repo() {
  # Essaie d’inférer owner/repo depuis l’origine git
  local url
  url="$(git remote get-url origin 2>/dev/null || true)"
  [[ -z "$url" ]] && fail "Impossible de déduire REPO: 'origin' introuvable. Exporte REPO=owner/repo."
  # gère ssh et https
  url="${url#git@github.com:}"
  url="${url#https://github.com/}"
  url="${url%.git}"
  echo "$url"
}

tag_exists_remote() {
  git ls-remote --tags "${1}" "refs/tags/${2}" | grep -q "${2}" || return 1
}

generate_notes() {
  case "$NOTES_MODE" in
    auto)
      # Laisser gh générer les notes automatiquement
      echo ""
      ;;
    changelog)
      # Notes basées sur les commits depuis le dernier tag
      local last_tag
      last_tag="$(git describe --tags --abbrev=0 2>/dev/null || true)"
      if [[ -n "$last_tag" ]]; then
        git log --pretty=format:'- %s (%h)' "${last_tag}..HEAD"
      else
        git log --pretty=format:'- %s (%h)'
      fi
      ;;
    file)
      [[ -n "$CHANGELOG_FILE" && -f "$CHANGELOG_FILE" ]] || fail "CHANGELOG_FILE invalide."
      cat "$CHANGELOG_FILE"
      ;;
    *)
      fail "NOTES_MODE invalide: $NOTES_MODE"
      ;;
  esac
}

# -------- Contrôles --------
need_cmd git
need_cmd gh

[[ -n "${REPO}" ]] || REPO="$(deduce_repo)"

# -------- Préparation du tag --------
if tag_exists_remote "https://github.com/${REPO}" "${TAG}"; then
  echo "Tag ${TAG} existe déjà sur ${REPO}. Suppression de la release et du tag existants..."
  # Supprime la release GitHub (--cleanup-tag supprime aussi le tag distant)
  gh release delete "$TAG" --repo "$REPO" --cleanup-tag --yes 2>/dev/null || true
  # Supprime le tag local s'il existe
  git tag -d "$TAG" 2>/dev/null || true
  echo "Release ${TAG} supprimée. Recréation en cours..."
fi

# Crée un tag local pointant sur la branche cible
git fetch origin "$BRANCH" --quiet
git checkout -q "$BRANCH"
git pull --ff-only --quiet

git tag -a "$TAG" -m "Release $TAG" || fail "Création du tag ${TAG} échouée."

if [[ "$PUSH_TAG" == "true" ]]; then
  git push origin "$TAG"
fi

# -------- Création de la release --------
NOTES="$(generate_notes || true)"

create_args=(release create "$TAG" --repo "$REPO" --title "$TITLE")
[[ "$PRERELEASE" == "true" ]] && create_args+=(--prerelease)
[[ "$DRAFT" == "true" ]] && create_args+=(--draft)
if [[ "$NOTES_MODE" == "auto" ]]; then
  create_args+=(--generate-notes)
else
  create_args+=(--notes "$NOTES")
fi
# Ajout des assets s'ils existent
for f in "${ASSETS[@]}"; do
  [[ -f "$f" ]] || fail "Asset introuvable: $f"
  create_args+=("$f")
done

gh "${create_args[@]}"

# ----- retour à la branche dev ------
git switch dev

echo "✅ Release ${TAG} créée sur ${REPO}."

