try:
    from landlockpy import AccessFS, AccessNet, Ruleset

    with Ruleset(
        handled_fs=AccessFS.NONE, handled_net=AccessNet.CONNECT_TCP
    ) as rs:
        rs.allow_port(53, AccessNet.CONNECT_TCP)
        rs.allow_port(80, AccessNet.CONNECT_TCP)
        rs.allow_port(443, AccessNet.CONNECT_TCP)
        rs.restrict()

except Exception as e:
    raise AssertionError(f"Landlock is not available: {e}")
