try:
    from landlockpy import AccessFS, AccessNet, Ruleset
except Exception as e:
    raise AssertionError(f"Landlock is not available: {e}")