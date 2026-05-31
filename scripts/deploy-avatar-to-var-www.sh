#!/usr/bin/env bash
# Копирование статического лендинга AVATARS на веб-корень.
# Запуск на сервере (обычно с sudo):
#   sudo ./scripts/deploy-avatar-to-var-www.sh
#   sudo ./scripts/deploy-avatar-to-var-www.sh --dry-run

set -euo pipefail

SOURCE="${SOURCE:-/home/develop/avatar2}"
DEST="${DEST:-/var/www/avatar2}"
OWNER="${OWNER:-www-data}"
GROUP="${GROUP:-www-data}"

DRY_RUN=0
if [[ "${1:-}" == "--dry-run" ]]; then
    DRY_RUN=1
fi

log() {
    printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

die() {
    log "ERROR: $*"
    exit 1
}

if [[ ! -d "$SOURCE" ]]; then
    die "Источник не найден: $SOURCE"
fi

if [[ ! -f "$SOURCE/index.html" ]]; then
    die "В $SOURCE нет index.html — проверьте путь к статике"
fi

if [[ $EUID -ne 0 ]]; then
    die "Нужны права root: sudo $0${1:+ $1}"
fi

RSYNC_OPTS=(-a --delete --human-readable)
if [[ $DRY_RUN -eq 1 ]]; then
    RSYNC_OPTS+=(--dry-run --itemize-changes)
    log "Режим проверки (файлы не изменяются)"
fi

log "Источник:  $SOURCE"
log "Назначение: $DEST"

mkdir -p "$DEST"

if command -v rsync >/dev/null 2>&1; then
    rsync "${RSYNC_OPTS[@]}" \
        --exclude '.git' \
        --exclude '.gitignore' \
        --exclude 'deploy-avatar-to-var-www.sh' \
        "$SOURCE/" "$DEST/"
else
    log "rsync не найден, используется cp"
  if [[ $DRY_RUN -eq 1 ]]; then
        log "(dry-run) cp -a $SOURCE/* -> $DEST/"
    else
        cp -a "$SOURCE/." "$DEST/"
    fi
fi

if [[ $DRY_RUN -eq 0 ]]; then
    chown -R "$OWNER:$GROUP" "$DEST"
    find "$DEST" -type d -exec chmod 755 {} \;
    find "$DEST" -type f -exec chmod 644 {} \;
    log "Права: $OWNER:$GROUP, каталоги 755, файлы 644"
fi

log "Готово."
log "Проверка: curl -I http://127.0.0.1/  (если vhost указывает на $DEST)"
