#!/bin/sh
set -eu
cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
  cp .env.example .env
  echo "Created .env from .env.example"
fi

docker compose up -d db wordpress
docker compose --profile setup run --rm wpcli

url="$(grep -E '^WP_URL=' .env | head -n 1 | cut -d= -f2- | tr -d '"' | tr -d "'")"
if [ -z "$url" ]; then
  url="http://localhost:8080"
fi

echo "Checking $url …"
i=0
while [ "$i" -lt 30 ]; do
  if curl -fsS "$url/" | grep -q "KayaVue"; then
    echo "Ready: $url"
    exit 0
  fi
  i=$((i + 1))
  sleep 2
done

echo "WordPress did not answer at $url with the KayaVue theme." >&2
exit 1
