// PreventScreenshot.m
// ---------------------------------------------------------------------
// iOS native bridge for screenshot/screen-record defence.
//
// iOS does NOT expose an equivalent of FLAG_SECURE that blocks screenshots
// outright. The best legal+supported approach is:
//   (1) Listen for UIApplicationUserDidTakeScreenshotNotification and tell
//       the JS layer so we can log it to contact_access_log.
//   (2) Use UIScreen.mainScreen.isCaptured to hide the secure contact UI
//       while screen recording or AirPlay mirroring is active.
//   (3) Optionally cover the snapshot frame the OS takes for the app
//       switcher with a blurred overlay so contacts don't appear there.
//
// Module name as seen from JS: PreventScreenshot
//   - PreventScreenshot.startMonitoring()
//   - PreventScreenshot.stopMonitoring()
//   - PreventScreenshot.isBeingCaptured()  -> bool
//   - Emits "screenshot_taken" and "capture_state_changed" events.

#import <React/RCTBridgeModule.h>
#import <React/RCTEventEmitter.h>
#import <UIKit/UIKit.h>

@interface PreventScreenshot : RCTEventEmitter <RCTBridgeModule>
@property (nonatomic, assign) BOOL hasListeners;
@end

@implementation PreventScreenshot

RCT_EXPORT_MODULE();

- (NSArray<NSString *> *)supportedEvents {
    return @[@"screenshot_taken", @"capture_state_changed"];
}

- (void)startObserving { self.hasListeners = YES; }
- (void)stopObserving  { self.hasListeners = NO; }

RCT_EXPORT_METHOD(startMonitoring) {
    NSNotificationCenter *nc = [NSNotificationCenter defaultCenter];
    [nc removeObserver:self];

    [nc addObserver:self
           selector:@selector(_screenshotTaken:)
               name:UIApplicationUserDidTakeScreenshotNotification
             object:nil];

    [nc addObserver:self
           selector:@selector(_captureChanged:)
               name:UIScreenCapturedDidChangeNotification
             object:nil];
}

RCT_EXPORT_METHOD(stopMonitoring) {
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

RCT_EXPORT_METHOD(isBeingCaptured:(RCTPromiseResolveBlock)resolve
                          rejecter:(RCTPromiseRejectBlock)reject) {
    BOOL captured = UIScreen.mainScreen.isCaptured;
    resolve(@(captured));
}

- (void)_screenshotTaken:(NSNotification *)note {
    if (self.hasListeners) {
        [self sendEventWithName:@"screenshot_taken" body:@{@"at": @([[NSDate date] timeIntervalSince1970])}];
    }
}

- (void)_captureChanged:(NSNotification *)note {
    if (self.hasListeners) {
        [self sendEventWithName:@"capture_state_changed"
                           body:@{@"captured": @(UIScreen.mainScreen.isCaptured)}];
    }
}

+ (BOOL)requiresMainQueueSetup { return NO; }

@end
