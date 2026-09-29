try:
    pass
except Exception as e:
    raise AssertionError(f"Landlock is not available: {e}")
