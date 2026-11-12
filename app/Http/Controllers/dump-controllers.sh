for f in *.php; do
    echo "========== $f =========="
    cat "$f"
    echo ""
done > hasil-controllers.txt