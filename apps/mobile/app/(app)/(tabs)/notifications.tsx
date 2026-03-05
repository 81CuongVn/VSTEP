import { useRef } from "react";
import { Animated, StyleSheet, Text, View } from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { GradientBackground } from "@/components/GradientBackground";
import { StickyHeader, HEADER_H } from "@/components/StickyHeader";
import { useThemeColors, spacing, fontSize, fontFamily } from "@/theme";

export default function NotificationsScreen() {
  const c = useThemeColors();
  const insets = useSafeAreaInsets();
  const scrollY = useRef(new Animated.Value(0)).current;

  return (
    <View style={[styles.root, { backgroundColor: c.background }]}>
      <GradientBackground />
      <StickyHeader scrollY={scrollY} />
      <Animated.ScrollView
        style={styles.scroll}
        contentContainerStyle={[
          styles.content,
          {
            paddingTop: HEADER_H + insets.top + 8,
            paddingBottom: insets.bottom + 100,
          },
        ]}
        scrollEventThrottle={16}
        onScroll={Animated.event(
          [{ nativeEvent: { contentOffset: { y: scrollY } } }],
          { useNativeDriver: false },
        )}
      >
        <View style={styles.empty}>
          <Ionicons
            name="notifications-off-outline"
            size={64}
            color={c.mutedForeground}
          />
          <Text style={[styles.emptyTitle, { color: c.foreground }]}>
            Chưa có thông báo
          </Text>
          <Text style={[styles.emptySub, { color: c.mutedForeground }]}>
            Thông báo mới sẽ hiển thị ở đây
          </Text>
        </View>
      </Animated.ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1 },
  scroll: { flex: 1 },
  content: {
    flexGrow: 1,
    justifyContent: "center",
    alignItems: "center",
    paddingHorizontal: spacing.xl,
  },
  empty: {
    alignItems: "center",
    gap: spacing.sm,
  },
  emptyTitle: {
    fontSize: fontSize.lg,
    fontFamily: fontFamily.bold,
    marginTop: spacing.sm,
  },
  emptySub: {
    fontSize: fontSize.sm,
    textAlign: "center",
    fontFamily: fontFamily.regular,
  },
});
