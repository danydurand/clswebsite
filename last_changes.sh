#!/bin/bash

# Script to display all changes since the last push
# Handles both pushed and unpushed branches

echo "=================================="
echo "Changes Since Last Push"
echo "=================================="
echo ""

# Get current branch name
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo "Current branch: $CURRENT_BRANCH"
echo ""

# Check if branch has an upstream
UPSTREAM=$(git rev-parse --abbrev-ref --symbolic-full-name @{u} 2>/dev/null)

if [ $? -eq 0 ]; then
    # Branch has upstream - compare with it
    echo "Upstream: $UPSTREAM"
    echo ""
    
    # Show unpushed commits
    UNPUSHED_COUNT=$(git log @{push}..HEAD --oneline | wc -l)
    
    if [ $UNPUSHED_COUNT -gt 0 ]; then
        echo "📝 Unpushed Commits ($UNPUSHED_COUNT):"
        echo "----------------------------------"
        git log @{push}..HEAD --oneline --decorate
        echo ""
        
        echo "📊 Files Changed:"
        echo "----------------------------------"
        git diff --stat @{push}..HEAD
        echo ""
    else
        echo "✓ No unpushed commits"
        echo ""
    fi
else
    # No upstream - compare with main/master
    echo "⚠️  No upstream configured for this branch"
    echo ""
    
    # Try to find base branch
    if git rev-parse --verify origin/main >/dev/null 2>&1; then
        BASE_BRANCH="origin/main"
    elif git rev-parse --verify main >/dev/null 2>&1; then
        BASE_BRANCH="main"
    elif git rev-parse --verify origin/master >/dev/null 2>&1; then
        BASE_BRANCH="origin/master"
    elif git rev-parse --verify master >/dev/null 2>&1; then
        BASE_BRANCH="master"
    else
        echo "❌ Could not find base branch (main/master)"
        BASE_BRANCH=""
    fi
    
    if [ -n "$BASE_BRANCH" ]; then
        echo "Comparing with: $BASE_BRANCH"
        echo ""
        
        COMMIT_COUNT=$(git log $BASE_BRANCH..HEAD --oneline | wc -l)
        
        if [ $COMMIT_COUNT -gt 0 ]; then
            echo "📝 Commits on this branch ($COMMIT_COUNT):"
            echo "----------------------------------"
            git log $BASE_BRANCH..HEAD --oneline --decorate
            echo ""
            
            echo "📊 Files Changed:"
            echo "----------------------------------"
            git diff --stat $BASE_BRANCH...HEAD
            echo ""
        else
            echo "✓ No commits on this branch"
            echo ""
        fi
    fi
fi

# Show uncommitted changes
UNCOMMITTED=$(git status --short)

if [ -n "$UNCOMMITTED" ]; then
    echo "🔧 Uncommitted Changes:"
    echo "----------------------------------"
    git status --short
    echo ""
    
    echo "📊 Uncommitted Diff Stats:"
    echo "----------------------------------"
    git diff --stat
    git diff --stat --cached
    echo ""
else
    echo "✓ No uncommitted changes"
    echo ""
fi

echo "=================================="
