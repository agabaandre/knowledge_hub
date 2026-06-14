#!/usr/bin/env bash
# Records search security smoke tests (SQL injection, XSS probes, validation).
# Usage:
#   ./scripts/security-test-records-search.sh
#   ./scripts/security-test-records-search.sh https://khub.africacdc.org
#   ./scripts/security-test-records-search.sh http://localhost/knowledge_hub

set -euo pipefail

BASE="${1:-https://khub.africacdc.org}"
UA="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
BODY_FILE="${TMPDIR:-/tmp}/khub_sec_body.html"

run_test() {
  local label="$1"
  local url="$2"
  local accept="${3:-}"
  local mode="${4:-follow}"
  local code time_s body leak result curl_args=()

  if [ "$mode" = "no-follow" ]; then
    curl_args=(-s -A "$UA" --max-redirs 0)
  else
    curl_args=(-sL -A "$UA")
  fi
  if [ -n "$accept" ]; then
    curl_args+=(-H "Accept: $accept")
  fi

  code=$(curl "${curl_args[@]}" -o "$BODY_FILE" -w "%{http_code}" --max-time 45 "$url" 2>/dev/null || echo "000")
  time_s=$(curl "${curl_args[@]}" -o /dev/null -w "%{time_total}" --max-time 45 "$url" 2>/dev/null || echo "0")
  body=$(wc -c < "$BODY_FILE" 2>/dev/null | tr -d ' ')
  leak=""

  if rg -qi "SQLSTATE\[|PDOException|Illuminate\\\\Database\\\\QueryException|syntax error at line" "$BODY_FILE" 2>/dev/null; then
    leak=$(rg -oi "SQLSTATE[^<]{0,80}" "$BODY_FILE" 2>/dev/null | head -1 | tr '\n' ' ')
  fi

  result="HTTP $code"
  case "$code" in
    200) result="OK (200)" ;;
    422) result="REJECTED (422)" ;;
    403) result="FORBIDDEN (403)" ;;
    500) result="ERROR (500)" ;;
    301|302) result="REDIRECT ($code)" ;;
  esac

  if [ -n "$leak" ]; then
    result="$result + SQL ERROR LEAK"
  fi

  printf '%s | %s | %ss | %s bytes\n' "$label" "$result" "$time_s" "$body"
}

echo "Security test run: $(date -u '+%Y-%m-%d %H:%M:%S UTC')"
echo "Target: $BASE"
echo "User-Agent: browser (avoids BotProtection false blocks on curl)"
echo "========================================"

run_test "baseline_home" "${BASE}/"
run_test "search_empty" "${BASE}/records/search?term=&rcc=all&country_id=&thematic_area_id=&sub_thematic_area_id=&file_category_id=&data_category_id=&author_id=&file_type_id="
run_test "term_sql_or" "${BASE}/records/search?term=test%27%20OR%201%3D1--"
run_test "term_union" "${BASE}/records/search?term=test%27%20UNION%20SELECT%201,2,3--"
run_test "country_sqli" "${BASE}/records/search?country_id=1%20OR%201%3D1"
run_test "author_sqli" "${BASE}/records/search?author_id=1%27%20OR%20%271%27%3D%271"
run_test "file_type_sqli" "${BASE}/records/search?file_type_id=1%3B%20DROP%20TABLE%20users--"
run_test "thematic_sqli" "${BASE}/records/search?thematic_area_id=1%20AND%201%3D1"
run_test "tag_sqli" "${BASE}/records/search?tag=1%20OR%201%3D1"
run_test "rcc_sqli" "${BASE}/records/search?rcc=1%20OR%201%3D1"
run_test "rcc_all" "${BASE}/records/search?rcc=all"
run_test "country_invalid" "${BASE}/records/search?country_id=abc"
run_test "data_cat_invalid" "${BASE}/records/search?data_category_id=abc"
run_test "term_xss" "${BASE}/records/search?term=%3Cscript%3Ealert(1)%3C%2Fscript%3E"
run_test "fragment_sqli" "${BASE}/records/search/fragment?term=test%27%20OR%201%3D1--&country_id=1%20OR%201%3D1" "application/json"
run_test "admin_unauth" "${BASE}/admin" "" "no-follow"
run_test "path_traversal" "${BASE}/records/search?term=..%2F..%2Fetc%2Fpasswd"
